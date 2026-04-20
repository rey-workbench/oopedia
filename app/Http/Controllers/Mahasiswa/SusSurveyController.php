<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\SusResultServiceInterface;
use App\DTOs\Survey\SusResultCreateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreSusResultRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class SusSurveyController extends Controller
{
    public function __construct(
        protected SusResultServiceInterface $susService,
    ) {}

    public function create(): Response|RedirectResponse
    {
        if ($this->susService->hasUserSubmitted((string) Auth::id())) {
            return redirect()->route('mahasiswa.sus-survey.thankyou');
        }

        $questions = $this->getQuestions();

        return $this->render('Mahasiswa/Sus/Create/Index', compact('questions'));
    }

    public function store(StoreSusResultRequest $request): RedirectResponse
    {
        if ($this->susService->hasUserSubmitted((string) Auth::id())) {
            return redirect()->route('mahasiswa.sus-survey.thankyou');
        }

        $dto = SusResultCreateDTO::fromRequest($request, (string) Auth::id());

        $this->susService->submitResult($dto->toArray());

        return redirect()->route('mahasiswa.sus-survey.thankyou');
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Sus/ThankYou/Index');
    }

    /** @return array<int, array<string, string>> */
    private function getQuestions(): array
    {
        return [
            ['id' => 'q1', 'text' => 'Saya merasa akan sering menggunakan sistem ini.'],
            ['id' => 'q2', 'text' => 'Saya merasa sistem ini tidak perlu rumit.'],
            ['id' => 'q3', 'text' => 'Saya merasa sistem ini mudah untuk digunakan.'],
            ['id' => 'q4', 'text' => 'Saya merasa butuh bantuan dari orang teknis untuk mengoperasikan sistem ini.'],
            ['id' => 'q5', 'text' => 'Saya merasa fitur-fitur dalam sistem ini terintegrasi dengan baik.'],
            ['id' => 'q6', 'text' => 'Saya merasa ada banyak ketidakkonsistenan di dalam sistem ini.'],
            ['id' => 'q7', 'text' => 'Saya membayangkan kebanyakan orang akan belajar menggunakan sistem ini dengan sangat cepat.'],
            ['id' => 'q8', 'text' => 'Saya merasa sistem ini sangat sulit/melelahkan untuk digunakan.'],
            ['id' => 'q9', 'text' => 'Saya merasa sangat percaya diri saat menggunakan sistem ini.'],
            ['id' => 'q10', 'text' => 'Saya perlu mempelajari banyak hal sebelum saya bisa mulai mengoperasikan sistem ini.'],
        ];
    }
}
