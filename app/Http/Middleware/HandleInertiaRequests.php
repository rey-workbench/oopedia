<?php

namespace App\Http\Middleware;

use Illuminate\Support\Collection;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    #[\Override]
    protected $rootView = 'app';

    public function __construct(
        protected ?MaterialServiceInterface $materialService = null,
        protected ?PerformanceServiceInterface $performanceService = null,
        protected ?UserServiceInterface $userService = null,
    ) {}

    #[\Override]
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            
            'auth' => [
                'user' => fn () => $user ? new UserResource($user)->resolve() : null,
            ],
            
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'status'  => fn () => $request->session()->get('status'),
            ],
            'feedback' => fn () => $request->session()->get('feedback'),
            
            'sidebar_materials' => fn (): Collection => $this->materialService instanceof MaterialServiceInterface
                ? $this->materialService->getSidebarMaterials($user?->id, ! $user)
                : collect(),
                
            'pending_admins_count' => fn (): int => 
                ($user && $user->isSuperAdmin() && $this->userService instanceof UserServiceInterface) 
                    ? $this->userService->getPendingAdminsCount() 
                    : 0,
                    
            'student_state' => function () use ($user, $request) {
                if ($user && $this->performanceService instanceof PerformanceServiceInterface) {
                    return $this->performanceService->getStudentSessionState((string) $user->id);
                }

                return $request->session()->get('student_state');
            },
            
            'csrf_token' => csrf_token(),
        ];
    }
}
