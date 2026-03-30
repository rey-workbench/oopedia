<?php

namespace App\Http\Middleware;

use App\Models\Material;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'status'  => fn () => $request->session()->get('status'),
            ],
            'sidebar_materials' => $this->getSidebarMaterials($request->user()),
            'csrf_token'        => csrf_token(),
        ];
    }

    /**
     * Get sidebar materials with locked status based on user role.
     *
     * @return Collection
     */
    protected function getSidebarMaterials($user)
    {
        $isGuest = ! $user;

        // For guests, cache without user-specific data (all unlocked for guests)
        if ($isGuest) {
            return Cache::remember('sidebar_materials_guest', 3600, function () {
                return Material::orderBy('created_at', 'asc')
                    ->select('id', 'title')
                    ->get()
                    ->map(function ($material) {
                        $material->is_locked = false;

                        return $material;
                    });
            });
        }

        // For authenticated users, get their unlocked modules
        $studentState    = StudentState::where('user_id', $user->id)->first();
        $unlockedModules = $studentState?->learning_profile['unlocked_modules'] ?? [];

        $materials = Material::orderBy('created_at', 'asc')
            ->select('id', 'title', 'module_id')
            ->get()
            ->map(function ($material) use ($unlockedModules) {
                $moduleId            = $material->module_id;
                $isFirstModule       = $moduleId !== null && $moduleId == 1;
                $isUnlocked          = empty($moduleId) || $isFirstModule || in_array($moduleId, $unlockedModules);
                $material->is_locked = ! $isUnlocked;

                return $material;
            });

        return $materials;
    }
}
