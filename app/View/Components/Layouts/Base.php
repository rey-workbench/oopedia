<?php

namespace App\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Base extends Component
{
    /**
     * Page title.
     */
    public string $title;

    /**
     * Additional body classes.
     */
    public string $bodyClass;

    /**
     * Meta tags configuration.
     *
     * @var array<string, string>
     */
    public array $meta;

    /**
     * User role identifier.
     */
    public string $role;

    /**
     * Theme to use (admin or student).
     */
    public string $theme;

    /**
     * Create a new component instance.
     *
     * @param array<string, string> $meta
     */
    public function __construct(
        string $title = 'OOPEDIAv2',
        string $bodyClass = '',
        array $meta = [],
        ?string $role = null,
    ) {
        $this->title     = $title;
        $this->bodyClass = $bodyClass;
        $this->meta      = $meta;
        $this->role      = $role ?? $this->detectRole();
        $this->theme     = $this->determineTheme();
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
     * Determine theme based on role.
     */
    protected function determineTheme(): string
    {
        return match ($this->role) {
            'superadmin', 'admin' => 'admin',
            'mahasiswa', 'guest' => 'student',
            default => 'student',
        };
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
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.base');
    }
}
