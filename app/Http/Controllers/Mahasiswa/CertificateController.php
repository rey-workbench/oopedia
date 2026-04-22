<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class CertificateController extends Controller
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function index(): Response
    {
        $userId = Auth::id();
        $state  = $this->progressRepo->getOrCreateStudentState($userId);

        /** @var array<string, string> $rawCertifications */
        $rawCertifications = $state->certifications ?? [];

        $certifications = collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepo->find($materialId);

                return [
                    'material_id'    => $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type'           => $type,
                    'issued_at'      => null,
                ];
            })
            ->values()
            ->toArray();

        return $this->render('Mahasiswa/Certificates/Index', [
            'certifications' => $certifications,
        ]);
    }
}
