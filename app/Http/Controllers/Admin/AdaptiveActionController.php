<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdaptiveAction\StoreAdaptiveActionRequest;
use App\Http\Requests\Admin\AdaptiveAction\UpdateAdaptiveActionRequest;
use App\Models\AdaptiveAction;
use Illuminate\Http\RedirectResponse;

final class AdaptiveActionController extends Controller
{
    public function __construct(
        private readonly AdaptiveManagementServiceInterface $adaptiveManagementService,
    ) {}

    public function update(UpdateAdaptiveActionRequest $request, AdaptiveAction $adaptive_action): RedirectResponse
    {
        $dto = AdaptiveActionDTO::fromRequest($request, $adaptive_action->id);
        $this->adaptiveManagementService->updateAction($adaptive_action->id, $dto);

        return back()->with('success', 'Aksi adaptif berhasil diperbarui.');
    }

    public function store(StoreAdaptiveActionRequest $request): RedirectResponse
    {
        $dto = AdaptiveActionDTO::fromRequest($request);
        $this->adaptiveManagementService->createAction($dto);

        return back()->with('success', 'Aksi adaptif baru berhasil dibuat.');
    }

    public function destroy(AdaptiveAction $adaptive_action): RedirectResponse
    {
        try {
            $this->adaptiveManagementService->deleteAction($adaptive_action->id);

            return back()->with('success', 'Aksi adaptif berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
