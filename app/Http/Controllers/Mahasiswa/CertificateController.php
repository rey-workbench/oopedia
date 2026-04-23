<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;

final class CertificateController extends Controller
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
    ) {
    }

    public function index(): Response
    {
        $userId = Auth::id();
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        $rawCertifications = $state->certifications ?? [];

        $certifications = collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepo->find($materialId);

                return [
                    'material_id' => $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type' => $type,
                    'issued_at' => null,
                ];
            })
            ->values()
            ->toArray();

        return $this->render('Mahasiswa/Certificates/Index', [
            'certifications' => $certifications,
        ]);
    }

    public function preview(string $materialId): \Illuminate\View\View
    {
        $userId = Auth::id();
        $user = \Auth::user();
        $state = $this->progressRepo->getOrCreateStudentState($userId);
        $material = $this->materialRepo->find($materialId);

        if (!$material) {
            abort(404, 'Material not found');
        }

        $rawCertifications = $state->certifications ?? [];
        $tier = $rawCertifications[$materialId] ?? 'gold'; // Default ke gold jika belum ada (untuk preview)



        $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path("images/logo.png")));
        $signature = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path("images/certificates/sign.png")));
        $tierPalettes = [
            'gold' => ['primary' => '#EAB308', 'dark' => '#854D0E'], // Yellow-500, Yellow-900
            'silver' => ['primary' => '#94A3B8', 'dark' => '#1E293B'], // Slate-400, Slate-900
            'bronze' => ['primary' => '#D97706', 'dark' => '#78350F'], // Amber-600, Amber-900
        ];

        $palette = $tierPalettes[$tier] ?? $tierPalettes['gold'];

        return view('pdf.certificate', [
            'student_name' => $user?->name ?? 'Student Name',
            'material_title' => $material->title,
            'material_id' => $material->id,
            'tier_name' => ucfirst($tier),
            'tier_color' => $palette['primary'],
            'tier_color_dark' => $palette['dark'],
            'logo_image' => $logo,
            'sign_image' => $signature,
            'date' => now()->translatedFormat('d F Y'),
        ]);
    }

    public function download(string $materialId): mixed
    {
        $userId = Auth::id();
        $user = Auth::user();
        $state = $this->progressRepo->getOrCreateStudentState($userId);
        $material = $this->materialRepo->find($materialId);

        if (!$material) {
            abort(404, 'Material not found');
        }

        $rawCertifications = $state->certifications ?? [];
        $tier = $rawCertifications[$materialId] ?? null;

        if (!$tier) {
            abort(403, 'You have not earned a certificate for this material');
        }

        $tierPalettes = [
            'gold' => ['primary' => '#EAB308', 'dark' => '#854D0E'], // Yellow-500, Yellow-900
            'silver' => ['primary' => '#94A3B8', 'dark' => '#1E293B'], // Slate-400, Slate-900
            'bronze' => ['primary' => '#D97706', 'dark' => '#78350F'], // Amber-600, Amber-900
        ];
        $palette = $tierPalettes[$tier] ?? $tierPalettes['gold'];

        $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path("images/logo.png")));
        $signature = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path("images/certificates/sign.png")));

        return Pdf::view('pdf.certificate', [
            'student_name' => $user?->name ?? 'Student',
            'material_title' => $material->title,
            'material_id' => $material->id,
            'tier_name' => ucfirst($tier),
            'tier_color' => $palette['primary'],
            'tier_color_dark' => $palette['dark'],
            'logo_image' => $logo,
            'sign_image' => $signature,
            'date' => now()->translatedFormat('d F Y'),
        ])
            ->landscape()
            ->name("Sertifikat_{$tier}_{$material->title}.pdf")
            ->download();
    }
}
