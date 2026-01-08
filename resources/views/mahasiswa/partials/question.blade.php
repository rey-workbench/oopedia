@php
    use App\Models\Progress;
@endphp

<div class="bg-white rounded-xl shadow-md p-6">

        @if(!isset($isGuest) || !$isGuest)
            @php $isGuest = !auth()->check(); @endphp
        @endif

        {{-- Restoring Stats Bar (Hints, XP, Streak) as per user request --}}
        @php
            // Fetch latest progress to get current stats
            $latestProgress = \App\Models\Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->latest()
                ->first();
            
            $attributes = $latestProgress ? ($latestProgress->attributes ?? []) : [];
            $xp = $attributes['xp'] ?? 0;
            $streak = $attributes['current_streak'] ?? 0;
            $points = $attributes['points'] ?? 0;
            $hintsAvailable = $attributes['hints_available'] ?? 3; // Default to 3 if not set
        @endphp

        <div class="mb-8 p-1 bg-gray-50 rounded-2xl flex items-center gap-1 shadow-inner">
            <div class="flex-1 px-6 py-3 rounded-xl bg-white shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-6">
                    {{-- Difficulty --}}
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Kesulitan</span>
                        <h5 class="text-lg font-black {{ $difficulty == 'hard' ? 'text-rose-600' : ($difficulty == 'medium' ? 'text-amber-600' : 'text-emerald-600') }}">
                            {{ ucfirst($difficulty) }}
                        </h5>
                    </div>
                    
                    {{-- XP Indicator --}}
                    <div class="xp-indicator">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">XP</span>
                        <h5 class="text-lg font-black text-blue-600 flex items-center gap-1">
                            <i class="fas fa-star text-amber-400 text-sm"></i> {{ $xp }}
                        </h5>
                    </div>

                    {{-- Streak Indicator --}}
                    <div class="streak-indicator">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Streak</span>
                        <h5 class="text-lg font-black text-orange-600 flex items-center gap-1">
                            <i class="fas fa-fire text-orange-500 text-sm"></i> {{ $streak }}
                        </h5>
                    </div>
                </div>

                {{-- Hint Button --}}
                <div>
                     <button type="button" id="hintBtn" class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 transition-all font-bold text-sm" {{ $hintsAvailable <= 0 ? 'disabled' : '' }}>
                        <div class="w-6 h-6 rounded-lg bg-indigo-200 group-hover:bg-indigo-300 flex items-center justify-center transition-colors">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <span>Hint (<span id="hintsCount">{{ $hintsAvailable }}</span>)</span>
                    </button>
                </div>
            </div>
        </div>

        <div id="questionContainer">
            @if($currentQuestion)
            <form id="questionForm"
                action="{{ route('mahasiswa.materials.questions.check', [
                    'material' => $material->id,
                    'question' => $currentQuestion->id,
                ]) }}"
                method="POST">
                @csrf
                <input type="hidden" name="used_hint" id="usedHintInput" value="false">
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

        {{-- Simplified Feedback Layer --}}
        <div class="exercise-feedback hidden fixed inset-0 z-[100] flex items-center justify-center pointer-events-none transition-all duration-300">
            <div id="hapticLayer" class="absolute inset-0 opacity-0 transition-opacity duration-300"></div>
            
            <div class="relative z-10 p-12 rounded-[3rem] text-center bg-white shadow-2xl scale-90 opacity-0 feedback-content-box transition-all duration-500 pointer-events-auto">
                <div id="feedbackIcon" class="text-8xl mb-6"></div>
                <div id="feedbackStatus" class="text-4xl font-black mb-8 italic uppercase tracking-tight"></div>

                <div id="explanationBox" class="hidden max-w-lg mx-auto mt-6 p-6 bg-slate-50 rounded-2xl border-l-8 border-blue-500 text-left">
                    <h5 class="font-bold text-lg mb-2 text-slate-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-500"></i>
                        Penjelasan
                    </h5>
                    <p id="explanationText" class="text-slate-600 leading-relaxed"></p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
                    <x-ui.button id="tryAgainBtn" variant="outline" class="px-10 py-4 rounded-2xl font-black italic uppercase tracking-widest text-sm">
                        <i class="fas fa-redo mr-2"></i> Coba Lagi
                    </x-ui.button>
                    <x-ui.button id="nextQuestionBtn" variant="primary" class="px-10 py-4 rounded-2xl font-black italic uppercase tracking-widest text-sm hidden">
                        Lanjut <i class="fas fa-arrow-right ml-2"></i>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
