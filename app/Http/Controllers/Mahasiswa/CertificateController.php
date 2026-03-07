<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CertificateController extends Controller
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function index(): Response
    {
        $userId = Auth::id();
        $state  = $this->progressRepo->getOrCreateStudentState($userId);

        /** @var array<string|int, string> $rawCertifications */
        $rawCertifications = $state->gamification_data['certifications'] ?? [];

        $certifications = collect($rawCertifications)
            ->map(function (string $type, int|string $materialId): array {
                $material = $this->materialRepo->find((int) $materialId);

                return [
                    'material_id'    => (int) $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type'           => $type, // gold | silver | bronze
                    'issued_at'      => null,
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Mahasiswa/Certificates/Index', [
            'certifications' => $certifications,
        ]);
    }
}
