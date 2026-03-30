<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\StudentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStudentController extends Controller
{
    public function __construct(protected StudentServiceInterface $studentService,
    ) {}

    public function index(Request $request): Response
    {
        $search   = $request->search;
        $students = $this->studentService->getStudentsWithProgress($search, 10);

        return Inertia::render('Admin/Students/Index', compact('students'));
    }

    public function show(string $studentId): Response|RedirectResponse
    {
        $student = $this->studentService->getStudentById($studentId);

        if (! $student || ! $student->isMahasiswa()) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Mahasiswa tidak ditemukan');
        }

        $data = $this->studentService->getStudentProgressDetail($student);

        return Inertia::render('Admin/Students/Progress/Index', [
            'student'                    => $student,
            'materials'                  => $data['materials'],
            'recent_activities'          => $data['recent_activities'],
            'missingQuestionsByMaterial' => $data['missingQuestionsByMaterial'],
            'certifications'             => $data['certifications'] ?? [],
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->studentService->createStudent($request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil didaftarkan secara manual.');
    }

    public function destroy(string $studentId): RedirectResponse
    {
        $this->studentService->deleteStudent($studentId);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data mahasiswa telah berhasil dihapus dari sistem');
    }

    public function showImportForm(): Response
    {
        return Inertia::render('Admin/Students/Import/Index');
    }

    public function processImport(ImportStudentRequest $request): RedirectResponse
    {
        $result = $this->studentService->importStudentsFromFile($request->file('excel_file'));

        $message = "Berhasil menambahkan {$result['success_count']} mahasiswa.";
        if (! empty($result['error_rows'])) {
            $message .= ' Terdapat ' . count($result['error_rows']) . ' baris dengan error.';
        }

        return redirect()->route('admin.students.index')
            ->with('success', $message)
            ->with('importErrors', $result['error_rows']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $template = $this->studentService->generateImportTemplate();

        return response()->stream($template['callback'], 200, $template['headers']);
    }
}
