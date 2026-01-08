<x-layouts.app :title="'Review Soal - ' . $material->title" theme="mahasiswa">
    <div class="container-fluid py-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1">
                <x-ui.card class="sticky top-4">
                    <div class="mb-4">
                        <h5 class="font-bold text-lg"><i class="fas fa-book mr-2"></i>Daftar Materi</h5>
                    </div>
                    <ul class="space-y-2">
                        @foreach($materials as $m)
                            <li>
                                <a href="{{ route('mahasiswa.materials.show', $m->id) }}"
                                   class="block p-3 rounded-lg transition-colors {{ $m->id == $material->id ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-file-alt mr-2"></i>{{ $m->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            </div>

            <div class="lg:col-span-3">
                <x-ui.card>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Review Soal {{ $difficulty !== 'all' ? ucfirst($difficulty) : 'Semua Tingkat' }}</h3>
                        <p class="text-gray-600">Berikut adalah review dari soal-soal yang telah Anda kerjakan.</p>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <x-ui.button
                                href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=all"
                                :variant="$difficulty == 'all' ? 'primary' : 'outline'"
                            >
                                Semua
                            </x-ui.button>

                            <x-ui.button
                                href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=beginner"
                                :variant="$difficulty == 'beginner' ? 'success' : 'outline'"
                            >
                                Beginner
                            </x-ui.button>

                            <x-ui.button
                                href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=medium"
                                :variant="$difficulty == 'medium' ? 'warning' : 'outline'"
                            >
                                Medium
                            </x-ui.button>

                            <x-ui.button
                                href="{{ route('mahasiswa.materials.questions.review', $material->id) }}?difficulty=advanced"
                                :variant="$difficulty == 'advanced' ? 'danger' : 'outline'"
                            >
                                Advanced
                            </x-ui.button>
                        </div>
                    </div>

                        @if($questions->count() > 0)
                            <div class="space-y-6">
                                @foreach($questions as $index => $question)
                                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-center mb-6">
                                            <span class="inline-flex items-center gap-2 font-bold text-gray-700">
                                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                                                    {{ $index + 1 }}
                                                </div>
                                                Soal dari {{ $questions->count() }}
                                            </span>
                                            <x-ui.badge variant="{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}" class="px-3 py-1">
                                                {{ ucfirst($question->difficulty) }}
                                            </x-ui.badge>
                                        </div>

                                        <div class="space-y-6">
                                            <div>
                                                <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                                    <i class="fas fa-question-circle text-blue-400"></i>
                                                    Pertanyaan
                                                </h5>
                                                <div class="p-5 bg-gray-50 rounded-xl text-gray-800 leading-relaxed border border-gray-100 italic">
                                                    {!! $question->question_text !!}
                                                </div>
                                            </div>

                                            <div>
                                                <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                                    <i class="fas fa-list-ul text-indigo-400"></i>
                                                    Pilihan Jawaban
                                                </h5>
                                                <div class="grid grid-cols-1 gap-3">
                                                    @foreach($question->answers as $answer)
                                                        <div class="p-4 rounded-xl flex items-start gap-4 transition-all {{ $answer->is_correct ? 'bg-green-50 border-2 border-green-200 shadow-sm' : 'bg-white border-2 border-gray-50 text-gray-500' }}">
                                                            @if($answer->is_correct)
                                                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center shrink-0 mt-0.5">
                                                                    <i class="fas fa-check text-white text-xs"></i>
                                                                </div>
                                                            @else
                                                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0 mt-0.5">
                                                                    <i class="fas fa-times text-gray-300 text-xs"></i>
                                                                </div>
                                                            @endif
                                                            <div class="flex-1 font-medium">
                                                                {!! $answer->answer_text !!}
                                                            </div>
                                                        </div>
                                                        @if($answer->is_correct && $answer->explanation)
                                                            <div class="mt-2 p-5 bg-blue-50/50 border-l-4 border-blue-400 rounded-r-xl">
                                                                <div class="flex items-center gap-2 font-bold text-blue-900 mb-1">
                                                                    <i class="fas fa-lightbulb text-blue-500"></i>
                                                                    Penjelasan:
                                                                </div>
                                                                <div class="text-blue-800 text-sm leading-relaxed">
                                                                    {!! $answer->explanation !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <x-ui.alert variant="info" icon="info-circle">
                                Tidak ada soal yang tersedia untuk ditampilkan.
                            </x-ui.alert>
                        @endif
                    </div>
                    <div class="mt-6 flex gap-3 justify-center">
                        <x-ui.button
                            href="{{ route('mahasiswa.materials.questions.show', $material->id) }}?difficulty={{ $difficulty }}"
                            variant="primary"
                            icon="fas fa-arrow-left"
                        >
                            Kembali ke Soal
                        </x-ui.button>

                        <x-ui.button
                            href="{{ route('mahasiswa.materials.show', $material->id) }}"
                            variant="secondary"
                            icon="fas fa-book"
                        >
                            Kembali ke Materi
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
    </div>
</x-layouts.app>
