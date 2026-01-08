<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\StudentService;

class AdminStudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk melihat daftar mahasiswa');
        }
        
        $search = $request->search;
        $students = $this->studentService->getStudentsWithProgress($search, 10);
        
        return view('admin.students.index', compact('students'));
    }

    public function progress(User $student)
    {
        // Ensure we're looking at a student
        abort_if($student->role_id != 3, 404);
    
        $data = $this->studentService->getStudentProgressDetail($student);

        return view('admin.students.progress', [
            'student' => $student,
            'materials' => $data['materials'],
            'recent_activities' => $data['recent_activities'],
            'missingQuestionsByMaterial' => $data['missingQuestionsByMaterial']
        ]);
    }

    public function store(Request $request)
    {
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menambah mahasiswa');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => 3, // Role student
                'is_approved' => true
            ]);

            return redirect()->route('admin.students.index')
                ->with('success', 'Mahasiswa berhasil didaftarkan secara manual.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mendaftarkan mahasiswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $student)
    {
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus mahasiswa');
        }
        
        try {
            $this->studentService->deleteStudent($student);
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Data mahasiswa telah berhasil dihapus dari sistem');
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        return view('admin.students.import');
    }
    
    public function processImport(Request $request)
    {
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
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
        // Admin and superadmin access check
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        $template = $this->studentService->generateImportTemplate();
        
        return response()->stream($template['callback'], 200, $template['headers']);
    }

    public function show(User $student)
    {
        if ($student->role_id !== 3) {
            abort(404);
        }

        $data = $this->studentService->getStudentProgressDetail($student);

        return view('admin.students.show', [
            'student' => $student,
            'materials' => $data['materials']
        ]);
    }
}