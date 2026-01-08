@php
    use App\Models\Progress;
@endphp

<div class="bg-white rounded-xl shadow-md p-6">

        @if(!isset($isGuest) || !$isGuest)
            @php $isGuest = !auth()->check(); @endphp
        @endif

        @if(!$isGuest)
        <div class="mb-8 p-1 bg-gray-50 rounded-2xl flex items-center gap-1 shadow-inner">
            <div class="flex-1 px-6 py-3 rounded-xl bg-white shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Tingkat Kesulitan</span>
                    <h5 class="text-lg font-black {{ $difficulty == 'hard' ? 'text-rose-600' : ($difficulty == 'medium' ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ ucfirst($difficulty) }}
                    </h5>
                </div>
                <div class="flex gap-1">
                    <div class="w-2 h-6 rounded-full {{ $difficulty == 'beginner' || $difficulty == 'medium' || $difficulty == 'hard' ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                    <div class="w-2 h-6 rounded-full {{ $difficulty == 'medium' || $difficulty == 'hard' ? 'bg-amber-500' : 'bg-gray-200' }}"></div>
                    <div class="w-2 h-6 rounded-full {{ $difficulty == 'hard' ? 'bg-rose-500' : 'bg-gray-200' }}"></div>
                </div>
            </div>
        </div>
        @else
        <div class="mb-8 p-5 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-5">
             <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shrink-0 shadow-lg shadow-amber-200">
                 <i class="fas fa-user-clock text-white text-xl"></i>
             </div>
             <div>
                 <h5 class="font-bold text-amber-900 mb-0.5">Mode Tamu / Preview</h5>
                 <p class="text-amber-700 text-sm">Login untuk menyimpan progres dan fitur lengkap.</p>
             </div>
        </div>
        @endif

        <!-- Hidden Inputs for JS Context -->
        <input type="hidden" id="currentLevel" value="{{ $difficulty }}">

        <div id="questionContainer">
            @if($currentQuestion)
            <form id="questionForm"
                action="{{ route('mahasiswa.materials.questions.check', [
                    'material' => $material->id,
                    'question' => $currentQuestion->id,
                ]) }}"
                method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">

                <div class="mb-6">
                    <div class="flex items-center justify-between">
                         <x-ui.badge variant="primary" size="lg">
                            <i class="fas fa-question-circle mr-2"></i> Soal
                        </x-ui.badge>
                        <x-ui.badge variant="secondary" size="lg">
                             {{ ucfirst($currentQuestion->difficulty) }} Question
                        </x-ui.badge>
                    </div>
                </div>

                <div class="answers-container">
                    @if ($currentQuestion->question_type === 'fill_in_the_blank')
                        <div class="mb-6">
                            <h5 class="text-lg font-semibold mb-3"><i class="fas fa-question mr-2"></i>Pertanyaan</h5>
                            <div class="whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">
                                {!! $currentQuestion->question_text !!}
                            </div>
                        </div>
                        <div class="p-4 mb-4 rounded-lg bg-blue-50 border border-blue-200">
                            <label for="fill_in_the_blank_answer" class="block font-medium mb-2 text-gray-700">Jawaban Anda:</label>
                            <x-ui.input
                                type="text"
                                name="fill_in_the_blank_answer"
                                id="fill_in_the_blank_answer"
                                placeholder="Ketik jawaban Anda di sini..."
                                required
                            />
                        </div>
                    @else
                        @if ($currentQuestion->question_type === 'drag_and_drop')
                            <div class="question-content">
                                <h5 class=""><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                                <div class="question-html"
                                    style="font-family: monospace; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                    @php
                                        // Hilangkan tag <p> dan </p> terlebih dahulu
                                        $rawText = str_replace(['<p>', '</p>'], ["", "\n"], $currentQuestion->question_text);

                                        // Hilangkan tag <br>
                                        $finalText = str_replace('<br>', "\n", $rawText);

                                        // Escape karakter < dan >
                                        $escapedText = str_replace(['<', '>'], ['&lt;', '&gt;'], $finalText);

                                        // Ganti [zone] dengan drop-zone span
                                        $zoneCount = substr_count($escapedText, '[zone]');
                                        for ($i = 1; $i <= $zoneCount; $i++) {
                                            $escapedText = preg_replace(
                                                '/\[zone\]/',
                                                '<span class="drop-zone" id="dropZone' .
                                                    $i .
                                                    '" data-zone="' .
                                                    $i .
                                                    '" data-user-answer=""></span>',
                                                $escapedText,
                                                1,
                                            );
                                        }
                                    @endphp
                                    <pre>{!! $escapedText !!}</pre>
                                </div>
                                {{-- Drag and Drop --}}

                                <input type="hidden" name="drag_and_drop_answers" id="dragAndDropAnswers">

                                <h5 class="mt-4 mb-3 text-lg font-semibold text-gray-800"><i class="fas fa-list-ul mr-2"></i>Pilihan Jawaban</h5>
                                <div class="drag-items flex flex-wrap gap-3 mt-2">
                                    @foreach ($currentQuestion->answers as $answer)
                                        <div class="draggable px-4 py-2 bg-white border-2 border-blue-200 text-blue-600 rounded-lg font-medium cursor-grab active:cursor-grabbing hover:bg-blue-50 hover:border-blue-400 transition-all shadow-sm" draggable="true"
                                            data-value="{{ $answer->answer_text }}">
                                            {{ $answer->answer_text }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-8">
                                <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="fas fa-question-circle text-blue-500"></i>
                                    Pertanyaan
                                </h5>
                                <div class="p-6 bg-gray-900 rounded-2xl shadow-xl border-4 border-gray-800 text-gray-100 font-mono text-lg leading-relaxed relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-10">
                                        <i class="fas fa-code text-6xl"></i>
                                    </div>
                                    <div class="relative z-10">
                                        {!! $currentQuestion->question_text !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-tasks text-indigo-500"></i>
                                Pilih Jawaban
                            </h5>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($currentQuestion->answers as $answer)
                                    <label class="group relative block transition-all cursor-pointer">
                                        <input type="radio" name="answer" id="answer{{ $answer->id }}"
                                            value="{{ $answer->id }}" class="peer hidden" required>
                                        <div class="p-5 rounded-2xl border-2 border-gray-100 bg-white shadow-sm hover:border-blue-400 hover:bg-blue-50/30 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-md peer-checked:shadow-blue-100 flex items-center gap-4 transition-all">
                                            <div class="w-8 h-8 rounded-full border-2 border-gray-200 group-hover:border-blue-400 peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center shrink-0 transition-all">
                                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                            <div class="flex-1 text-gray-700 font-bold group-hover:text-blue-900 peer-checked:text-blue-900 transition-colors">
                                                {{ $answer->answer_text }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <div class="mt-6">
                    <x-ui.button type="submit" id="checkAnswerBtn" variant="primary" class="w-full py-3">
                        <i class="fas fa-check-circle mr-2"></i>Periksa Jawaban
                    </x-ui.button>
                </div>
            </form>
            @else
                <x-ui.empty-state
                    icon="trophy"
                    title="Level Completed!"
                    message="You have answered all questions in the {{ ucfirst($difficulty) }} level. Continue practicing to maintain your streak or wait for new content."
                />
            @endif
        </div>

        <div class="exercise-feedback hidden">
            <div class="p-8 rounded-2xl text-center">
                <div id="feedbackIcon" class="text-7xl mb-6">
                </div>
                <div id="feedbackStatus" class="text-3xl font-black mb-4">
                </div>

                <div id="adaptiveFeedback" class="hidden mt-6 p-5 rounded-xl text-left">
                </div>

                <div id="explanationBox" class="hidden mt-6 p-6 bg-blue-50 rounded-2xl border-l-8 border-blue-400 text-left shadow-sm">
                    <h5 class="font-bold text-xl mb-3 text-blue-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-blue-500"></i>
                        Penjelasan
                    </h5>
                    <p id="explanationText" class="mb-0 text-blue-800 leading-relaxed"></p>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-4 justify-center mt-10">
                    <x-ui.button id="tryAgainBtn" variant="outline" class="px-8 py-3 rounded-xl font-bold">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </x-ui.button>
                    <x-ui.button id="nextQuestionBtn" variant="success" class="px-8 py-3 rounded-xl font-bold hidden shadow-lg shadow-green-100">
                        Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ml-2"></i>
                    </x-ui.button>
                     <x-ui.button href="{{ route('mahasiswa.dashboard') }}" id="dashboardBtn" variant="secondary" class="px-8 py-3 rounded-xl font-bold hidden">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
