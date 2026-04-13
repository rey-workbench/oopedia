<?php

namespace App\View\Components\Navigation;

use App\Models\Material;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public string $activePage;

    public string $role;

    public string $userName;

    public string $userRole;

    public $materials;

    public string $currentRoute;

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
        $this->materials    = $materials                        ?? $this->loadMaterials();
        $this->currentRoute = request()->route()?->getName()    ?? '';
    }

    protected function detectRole(): string
    {
        if (! Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        return $user->role?->role_name ?? 'guest';
    }

    protected function getRoleLabel(): string
    {
        if (! Auth::check()) {
            return 'Guest';
        }
        $role = Auth::user()->role?->role_name ?? 'guest';

        return match ($role) {
            'superadmin' => 'Super Admin',
            'dosen'      => 'Admin',
            'mahasiswa'  => 'Mahasiswa',
            'guest'      => 'Guest',
            default      => 'User',
        };
    }

    protected function loadMaterials(): Collection
    {
        return Material::orderBy('created_at', 'asc')->get();
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isStudentRole(): bool
    {
        return in_array($this->role, ['mahasiswa', 'guest']);
    }

    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function isGuest(): bool
    {
        return ! Auth::check() || (Auth::user()->role?->role_name ?? 'guest') === 'guest';
    }

    public function isRouteActive(string $routePattern): bool
    {
        return request()->routeIs($routePattern);
    }

    public function getDashboardRoute(): string
    {
        if (! Auth::check()) {
            return 'mahasiswa.dashboard';
        }

        $role = Auth::user()->role?->role_name ?? 'mahasiswa';

        return $role === 'mahasiswa' ? 'mahasiswa.dashboard' : 'admin.dashboard';
    }

    public function getPendingAdminsCount(): int
    {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            return 0;
        }

        return User::whereHas('role', function ($q) {
            $q->where('role_name', 'dosen');
        })->where('is_approved', false)->count();
    }

    public function getSidebarMaterials(): Collection
    {
        if ($this->isGuest()) {
            return $this->materials->take(ceil($this->materials->count() / 2));
        }

        return $this->materials;
    }

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

    public function render(): View|Closure|string
    {
        return view('components.navigation.sidebar');
    }
}
