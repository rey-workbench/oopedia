<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdaptiveAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdaptiveActionController extends Controller
{
    public function update(Request $request, AdaptiveAction $adaptive_action): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'variant'      => 'nullable|string',
            'instructions' => 'required|array',
        ]);

        $adaptive_action->update($validated);

        return back()->with('success', 'Aksi adaptif berhasil diperbarui.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'         => 'required|string|unique:adaptive_actions,code',
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'variant'      => 'nullable|string',
            'instructions' => 'required|array',
        ]);

        AdaptiveAction::create($validated);

        return back()->with('success', 'Aksi adaptif baru berhasil dibuat.');
    }

    public function destroy(AdaptiveAction $adaptive_action): RedirectResponse
    {
        if ($adaptive_action->rules()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus aksi yang masih digunakan oleh aturan.');
        }

        $adaptive_action->delete();

        return back()->with('success', 'Aksi adaptif berhasil dihapus.');
    }
}
