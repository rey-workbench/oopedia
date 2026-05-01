<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\StudentServiceInterface;
use App\DTOs\User\StudentCreateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ImportStudentRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AdminStudentController extends Controller
{
    public function __construct(
        private readonly StudentServiceInterface $studentService,
    ) {}

    public function index(Request $request): Response
    {
        $search   = $request->search;
        $students = $this->studentService->getStudentsWithProgress($search, 10);

        return $this->render('Admin/Students/Index', ['students' => $students]);
    }

    public function show(string $studentId): Response|RedirectResponse
    {
        $student = $this->studentService->getStudentById($studentId);

        if (! $student instanceof User || ! $student->isMahasiswa()) {
            return to_route('admin.students.index')
                ->with('error', 'Mahasiswa tidak ditemukan');
        }

        $data = $this->studentService->getStudentProgressDetail($student);

        return $this->render('Admin/Students/Progress/Index', [
            'student'                    => $student,
            'materials'                  => $data['materials'],
            'recent_activities'          => $data['recent_activities'],
            'missingQuestionsByMaterial' => $data['missingQuestionsByMaterial'],
            'certifications'             => $data['certifications'] ?? [],
        ]);
    }

    public function store(StoreStudentRequest $storeStudentRequest): RedirectResponse
    {
        $studentCreateDTO = StudentCreateDTO::fromRequest($storeStudentRequest);

        $this->studentService->createStudent($studentCreateDTO->toArray());

        return to_route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil didaftarkan secara manual.');
    }

    public function destroy(string $studentId): RedirectResponse
    {
        $this->studentService->deleteStudent($studentId);

        return to_route('admin.students.index')
            ->with('success', 'Data mahasiswa telah berhasil dihapus dari sistem');
    }

    public function showImportForm(): Response
    {
        return $this->render('Admin/Students/Import/Index');
    }

    public function processImport(ImportStudentRequest $importStudentRequest): RedirectResponse
    {
        $result = $this->studentService->importStudentsFromFile($importStudentRequest->file('excel_file'));

        $message = sprintf('Berhasil menambahkan %s mahasiswa.', $result['success_count']);
        if (! empty($result['error_rows'])) {
            $message .= ' Terdapat ' . count($result['error_rows']) . ' baris dengan error.';
        }

        return to_route('admin.students.index')
            ->with('success', $message)
            ->with('importErrors', $result['error_rows']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $template = $this->studentService->generateImportTemplate();

        return response()->stream($template['callback'], 200, $template['headers']);
    }
}
