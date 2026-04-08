<?php

namespace App\Http\Middleware;

use App\Contracts\Services\MaterialServiceInterface;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(
        protected ?MaterialServiceInterface $materialService = null,
    ) {}

    public function share(Request $request): array
    {
        $user = $request->user();

        $sidebarMaterials = $this->materialService
            ? $this->materialService->getSidebarMaterials($user?->id, ! $user)
            : collect();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => [
                        'role_name' => $user->role?->role_name,
                    ],
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'status'  => fn () => $request->session()->get('status'),
            ],
            'sidebar_materials' => $sidebarMaterials,
            'csrf_token'        => csrf_token(),
        ];
    }
}
