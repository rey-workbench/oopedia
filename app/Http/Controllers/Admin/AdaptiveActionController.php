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
    ) {
    }

    public function update(UpdateAdaptiveActionRequest $updateAdaptiveActionRequest, AdaptiveAction $adaptiveAction): RedirectResponse
    {
        $adaptiveActionDTO = AdaptiveActionDTO::fromRequest($updateAdaptiveActionRequest, $adaptiveAction->id);
        $this->adaptiveManagementService->updateAction($adaptiveAction->id, $adaptiveActionDTO);

        return back()->with('success', 'Aksi adaptif berhasil diperbarui.');
    }

    public function store(StoreAdaptiveActionRequest $storeAdaptiveActionRequest): RedirectResponse
    {
        $adaptiveActionDTO = AdaptiveActionDTO::fromRequest($storeAdaptiveActionRequest);
        $this->adaptiveManagementService->createAction($adaptiveActionDTO);

        return back()->with('success', 'Aksi adaptif baru berhasil dibuat.');
    }

    public function destroy(AdaptiveAction $adaptiveAction): RedirectResponse
    {
        try {
            $this->adaptiveManagementService->deleteAction($adaptiveAction->id);

            return back()->with('success', 'Aksi adaptif berhasil dihapus.');
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
