<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\StudentServiceInterface;
use Inertia\Inertia;

class AdminStudentController extends Controller
{
    public function __construct(
        protected StudentServiceInterface $studentService
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $students = $this->studentService->getStudentsWithProgress($search, 10);
        
        return Inertia::render('Admin/Students/Index', compact('students'));
    }

    public function progress(int $studentId)
    {
        $student = $this->studentService->getStudentById($studentId);
        
        if (!$student || $student->role_id != 3) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Mahasiswa tidak ditemukan');
        }
    
        $data = $this->studentService->getStudentProgressDetail($student);

        return Inertia::render('Admin/Students/Progress/Index', [
            'student' => $student,
            'materials' => $data['materials'],
            'recent_activities' => $data['recent_activities'],
            'missingQuestionsByMaterial' => $data['missingQuestionsByMaterial']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->studentService->createStudent([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'], // Will be hashed in service
            ]);

            return redirect()->route('admin.students.index')
                ->with('success', 'Mahasiswa berhasil didaftarkan secara manual.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mendaftarkan mahasiswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $studentId)
    {
        try {
            $this->studentService->deleteStudent($studentId);
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Data mahasiswa telah berhasil dihapus dari sistem');
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        return Inertia::render('Admin/Students/Import/Index');
    }
    
    public function processImport(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ]);
        
        try {
            $result = $this->studentService->importStudentsFromFile($request->file('excel_file'));
            
            $message = "Berhasil menambahkan {$result['success_count']} mahasiswa.";
            if (!empty($result['error_rows'])) {
                $message .= " Terdapat " . count($result['error_rows']) . " baris dengan error.";
            }
            
            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('importErrors', $result['error_rows']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function downloadTemplate()
    {
        $template = $this->studentService->generateImportTemplate();
        
        return response()->stream($template['callback'], 200, $template['headers']);
    }

    public function show(int $studentId)
    {
        return redirect()->route('admin.students.progress', $studentId);
    }
}