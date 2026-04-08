<?php

namespace App\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Base extends Component
{
    public string $title;

    public string $bodyClass;

    /** @var array<string, string> */
    public array $meta;

    public string $role;

    public string $theme;

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

    protected function detectRole(): string
    {
        if (! Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        return $user->role?->role_name ?? 'guest';
    }

    protected function determineTheme(): string
    {
        return match ($this->role) {
            'superadmin', 'admin' => 'admin',
            'mahasiswa', 'guest' => 'student',
            default => 'student',
        };
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isStudentRole(): bool
    {
        return in_array($this->role, ['mahasiswa', 'guest']);
    }

    public function render(): View|Closure|string
    {
        return view('components.layouts.base');
    }
}
