<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\Material;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;

final class CertificateController extends Controller
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materialRepository,
        private readonly ProgressRepositoryInterface $progressRepository,
    ) {
    }

    public function index(): Response
    {
        $userId = Auth::id();
        $state  = $this->progressRepository->getOrCreateStudentState($userId);
        $rawCertifications = $state->certifications ?? [];

        $certifications = collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepository->find($materialId);

                return [
                    'material_id'    => $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type'           => $type,
                    'issued_at'      => null,
                ];
            })
            ->values();

        return $this->render('Mahasiswa/Certificates/Index', [
            'certifications' => CertificateResource::collection($certifications)->resolve(),
        ]);
    }

    public function preview(string $materialId, ?string $userId = null): View
    {
        $userId ??= Auth::id();

        if (! $userId) {
            abort(403, 'User not identified');
        }

        $user     = User::find($userId);
        $state    = $this->progressRepository->getOrCreateStudentState($userId);
        $material = $this->materialRepository->find($materialId);

        if (! $material instanceof Material) {
            abort(404, 'Materi tidak ditemukan');
        }

        $rawCertifications = $state->certifications          ?? [];
        $tier              = $rawCertifications[$materialId] ?? 'gold';

        $logo      = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        $signature = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/certificates/sign.png')));

        $qrUrl = route('mahasiswa.certificates.preview', [$materialId, $userId]);

        $logoPath = public_path('images/logo.png');

        $builder = new Builder(
            data: $qrUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: $logoPath,
            logoResizeToWidth: 80,
            logoPunchoutBackground: true,
        );

        $result        = $builder->build();
        $qrCodeDataUri = $result->getDataUri();

        $tierPalettes = [
            'gold'   => ['primary' => '#EAB308', 'dark' => '#854D0E'],
            'silver' => ['primary' => '#94A3B8', 'dark' => '#1E293B'],
            'bronze' => ['primary' => '#D97706', 'dark' => '#78350F'],
        ];

        $palette = $tierPalettes[$tier] ?? $tierPalettes['gold'];

        return view('pdf.certificate', [
            'student_name'    => $user?->name ?? 'Student Name',
            'material_title'  => $material->title,
            'material_id'     => $material->id,
            'tier_name'       => ucfirst($tier),
            'tier_color'      => $palette['primary'],
            'tier_color_dark' => $palette['dark'],
            'logo_image'      => $logo,
            'sign_image'      => $signature,
            'qr_code'         => $qrCodeDataUri,
            'date'            => now()->translatedFormat('d F Y'),
        ]);
    }

    public function download(string $materialId): mixed
    {
        $userId   = Auth::id();
        $user     = Auth::user();
        $state    = $this->progressRepository->getOrCreateStudentState($userId);
        $material = $this->materialRepository->find($materialId);

        if (! $material instanceof Material) {
            abort(404, 'Materi tidak ditemukan');
        }

        $rawCertifications = $state->certifications          ?? [];
        $tier              = $rawCertifications[$materialId] ?? null;

        if (! $tier) {
            abort(403, 'You have not earned a certificate for this material');
        }

        $tierPalettes = [
            'gold'   => ['primary' => '#EAB308', 'dark' => '#854D0E'], // Yellow-500, Yellow-900
            'silver' => ['primary' => '#94A3B8', 'dark' => '#1E293B'], // Slate-400, Slate-900
            'bronze' => ['primary' => '#D97706', 'dark' => '#78350F'], // Amber-600, Amber-900
        ];
        $palette = $tierPalettes[$tier] ?? $tierPalettes['gold'];

        $logo      = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        $signature = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/certificates/sign.png')));

        $qrUrl = route('mahasiswa.certificates.preview', [$materialId, $userId]);
        $logoPath = public_path('images/logo.png');

        $builder = new Builder(
            data: $qrUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: $logoPath,
            logoResizeToWidth: 80,
            logoPunchoutBackground: true,
        );
        $result = $builder->build();
        $qrCodeDataUri = $result->getDataUri();

        return Pdf::view('pdf.certificate', [
            'student_name'    => $user?->name ?? 'Student',
            'material_title'  => $material->title,
            'material_id'     => $material->id,
            'tier_name'       => ucfirst((string) $tier),
            'tier_color'      => $palette['primary'],
            'tier_color_dark' => $palette['dark'],
            'logo_image'      => $logo,
            'sign_image'      => $signature,
            'qr_code'         => $qrCodeDataUri,
            'date'            => now()->translatedFormat('d F Y'),
        ])
            ->landscape()
            ->name(sprintf('Sertifikat_%s_%s.pdf', $tier, $material->title))
            ->download();
    }
}
