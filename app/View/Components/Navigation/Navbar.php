<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Current page title for breadcrumb.
     */
    public string $titlePage;

    /**
     * User role identifier.
     */
    public string $role;

    /**
     * Navbar style variant.
     */
    public string $variant;

    /**
     * Current route name for active state.
     */
    public string $currentRoute;

    /**
     * User name for display.
     */
    public string $userName;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $titlePage = '',
        ?string $role = null,
        string $variant = 'default',
    ) {
        $this->titlePage    = $titlePage;
        $this->role         = $role ?? $this->detectRole();
        $this->variant      = $variant;
        $this->currentRoute = request()->route()?->getName() ?? '';
        $this->userName     = Auth::check() ? Auth::user()->name : '';
    }

    /**
     * Detect user role from authentication.
     */
    protected function detectRole(): string
    {
        if (! Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        return $user->role?->role_name ?? 'guest';
    }

    /**
     * Check if current role is admin-type.
     */
    public function isAdminRole(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if current role is student-type.
     */
    public function isStudentRole(): bool
    {
        return in_array($this->role, ['mahasiswa', 'guest']);
    }

    /**
     * Check if user is authenticated.
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Check if a route is currently active.
     */
    public function isRouteActive(string $routePattern): bool
    {
        return request()->routeIs($routePattern);
    }

    /**
     * Get navigation items for mahasiswa role.
     *
     * @return array<int, array{route: string, icon: string, label: string, pattern: string, badge?: string, badgeClass?: string}>
     */
    public function getMahasiswaNavItems(): array
    {
        $items = [];

        if ($this->isAuthenticated()) {
            $items[] = [
                'route'   => 'mahasiswa.dashboard',
                'icon'    => 'fa-home',
                'label'   => 'Dashboard',
                'pattern' => 'mahasiswa.dashboard*',
                'tooltip' => 'Dashboard pengguna',
            ];
        }

        $items[] = [
            'route'          => 'mahasiswa.materials.index',
            'icon'           => 'fa-book',
            'label'          => 'Materi',
            'pattern'        => 'mahasiswa.materials*',
            'excludePattern' => 'mahasiswa.materials.questions*',
            'tooltip'        => 'Kumpulan materi pembelajaran',
            'badge'          => ! $this->isAuthenticated() ? 'Terbatas' : null,
            'badgeClass'     => 'bg-warning text-dark',
        ];

        $items[] = [
            'route'      => 'mahasiswa.materials.questions.index',
            'icon'       => 'fa-clipboard-check',
            'label'      => 'Latihan Soal',
            'pattern'    => 'mahasiswa.materials.questions*',
            'tooltip'    => 'Latihan soal untuk menguji pemahaman',
            'badge'      => ! $this->isAuthenticated() ? 'Terbatas' : null,
            'badgeClass' => 'bg-warning text-dark',
        ];

        $items[] = [
            'route'      => 'mahasiswa.leaderboard',
            'icon'       => 'fa-trophy',
            'label'      => 'Peringkat',
            'pattern'    => 'mahasiswa.leaderboard*',
            'tooltip'    => 'Papan peringkat pengguna berdasarkan skor',
            'badge'      => ! $this->isAuthenticated() ? 'Perlu Login' : null,
            'badgeClass' => 'bg-danger text-white',
        ];

        return $items;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation.navbar');
    }
}
