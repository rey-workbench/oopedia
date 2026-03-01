<?php

namespace App\View\Components\Navigation;

use App\Models\Material;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Current active page identifier.
     */
    public string $activePage;

    /**
     * User role identifier.
     */
    public string $role;

    /**
     * User name for display.
     */
    public string $userName;

    /**
     * User role label for display.
     */
    public string $userRole;

    /**
     * Materials collection for menu.
     *
     * @var \Illuminate\Database\Eloquent\Collection|null
     */
    public $materials;

    /**
     * Current route name.
     */
    public string $currentRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $activePage = '',
        ?string $role = null,
        string $userName = '',
        string $userRole = '',
        $materials = null,
    ) {
        $this->activePage   = $activePage;
        $this->role         = $role ?? $this->detectRole();
        $this->userName     = $userName ?: (Auth::check() ? Auth::user()->name : 'Guest');
        $this->userRole     = $userRole ?: $this->getRoleLabel();
        $this->materials    = $materials                     ?? $this->loadMaterials();
        $this->currentRoute = request()->route()?->getName() ?? '';
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

        return match ($user->role_id) {
            1       => 'superadmin',
            2       => 'admin',
            3       => 'mahasiswa',
            4       => 'guest',
            default => 'guest',
        };
    }

    /**
     * Get role label for display.
     */
    protected function getRoleLabel(): string
    {
        if (! Auth::check()) {
            return 'Guest';
        }

        return match (Auth::user()->role_id) {
            1       => 'Super Admin',
            2       => 'Admin',
            3       => 'Mahasiswa',
            4       => 'Guest',
            default => 'Guest',
        };
    }

    /**
     * Load materials for sidebar menu.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function loadMaterials()
    {
        return Material::orderBy('created_at', 'asc')->get();
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
     * Check if user is guest (not logged in or role_id = 4).
     */
    public function isGuest(): bool
    {
        return ! Auth::check() || (Auth::check() && Auth::user()->role_id === 4);
    }

    /**
     * Check if a route is currently active.
     */
    public function isRouteActive(string $routePattern): bool
    {
        return request()->routeIs($routePattern);
    }

    /**
     * Get dashboard route based on role.
     */
    public function getDashboardRoute(): string
    {
        if (! Auth::check()) {
            return 'mahasiswa.dashboard';
        }

        return Auth::user()->role_id === 3 ? 'mahasiswa.dashboard' : 'admin.dashboard';
    }

    /**
     * Get pending admins count (for superadmin).
     */
    public function getPendingAdminsCount(): int
    {
        if (! Auth::check() || Auth::user()->role_id !== 1) {
            return 0;
        }

        return User::where('role_id', 2)->where('is_approved', false)->count();
    }

    /**
     * Get materials for sidebar based on user role.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSidebarMaterials()
    {
        $allMaterials = $this->materials;

        // If user is guest, only show half of the materials
        if ($this->isGuest()) {
            $totalMaterials  = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);

            return $allMaterials->take($materialsToShow);
        }

        return $allMaterials;
    }

    /**
     * Get sidebar title based on current route.
     */
    public function getSidebarTitle(): string
    {
        if ($this->isRouteActive('mahasiswa.dashboard*')) {
            return 'Dashboard';
        }

        if ($this->isRouteActive('mahasiswa.profile.show')) {
            return 'Profil';
        }

        if ($this->isRouteActive('mahasiswa.materials*') && ! $this->isRouteActive('mahasiswa.materials.questions*')) {
            return 'Daftar Materi';
        }

        if ($this->isRouteActive('mahasiswa.materials.questions*')) {
            return 'Latihan Soal';
        }

        if ($this->isRouteActive('mahasiswa.ueq-survey.create') || $this->isRouteActive('mahasiswa.ueq-survey.show')) {
            return 'User Experience Questionnaire';
        }

        return 'Pembelajaran';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation.sidebar');
    }
}
