<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200" theme="admin">
    <x-navigation.sidebar activePage="formulas" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navigation.navbar titlePage="Buat Formula Baru" />
        <div class="container-fluid py-4">
            <div class="row min-vh-80">
                <div class="col-12">
                    <x-ui.card class="shadow-lg h-100">
                        <x-slot:header>
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Buat Formula Baru</h6>
                            </div>
                        </x-slot:header>

                        <form action="{{ route('admin.formulas.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <x-forms.form-group label="Nama Formula" name="name" required>
                                        <x-ui.input name="name" placeholder="Contoh: Calculate Accuracy" value="{{ old('name') }}" required />
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.form-group label="Key (Unique)" name="key" required helpText="Lowercase, underscore only">
                                        <x-ui.input name="key" placeholder="Contoh: accuracy" value="{{ old('key') }}" pattern="[a-z_]+" required />
                                    </x-forms.form-group>
                                </div>
                            </div>

                            <x-forms.form-group label="Deskripsi" name="description">
                                <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi formula">{{ old('description') }}</textarea>
                            </x-forms.form-group>

                            <x-forms.form-group label="Expression" name="expression" required helpText="Gunakan fungsi: PERCENTAGE, IF, ROUND, MIN, MAX, dll">
                                <textarea name="expression" class="form-control font-monospace" rows="3" placeholder="Contoh: PERCENTAGE(correct_count, total_count)" required>{{ old('expression') }}</textarea>
                            </x-forms.form-group>

                            <div class="alert alert-info">
                                <strong>📚 Fungsi yang tersedia:</strong><br>
                                <code>PERCENTAGE(a, b)</code> - (a/b)*100<br>
                                <code>IF(condition, true_val, false_val)</code> - Kondisi<br>
                                <code>ROUND(value, decimals)</code> - Pembulatan<br>
                                <code>MIN(a, b)</code>, <code>MAX(a, b)</code> - Min/Max<br>
                                <code>ABS(value)</code> - Nilai absolut
                            </div>

                            <div class="alert alert-success">
                                <strong>📋 Available Fields (Attributes):</strong>
                                <div class="row mt-2">
                                    @foreach($attributes as $category => $attrs)
                                    <div class="col-md-6 mb-3">
                                        <small class="d-block"><strong>{{ ucfirst($category) }} Scope:</strong></small>
                                        @foreach($attrs as $attr)
                                        <code class="text-xs">{{ $attr->key }}</code> - {{ $attr->label }}<br>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>
                                <hr>
                                <strong>💡 Contoh Formula:</strong><br>
                                <code class="text-xs">PERCENTAGE(correct_count, total_count)</code> - Akurasi<br>
                                <code class="text-xs">IF(current_streak >= 5, 1, 0)</code> - High performer check<br>
                                <code class="text-xs">ROUND(wrong_count / total_count * 100, 2)</code> - Wrong rate
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <x-forms.form-group label="Return Type" name="return_type" required>
                                        <select name="return_type" class="form-control" required>
                                            @foreach(\App\Models\Formula::RETURN_TYPES as $key => $label)
                                                <option value="{{ $key }}" {{ old('return_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-forms.form-group label="Scope" name="scope" required>
                                        <select name="scope" class="form-control" required>
                                            @foreach(\App\Models\Formula::SCOPES as $key => $label)
                                                <option value="{{ $key }}" {{ old('scope', 'material') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </x-forms.form-group>
                                </div>
                                <div class="col-md-4">
                                    <div class="mt-4">
                                        <x-forms.checkbox name="is_active" label="Aktifkan Formula" checked />
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <x-ui.button variant="outline" size="lg" href="{{ route('admin.formulas.index') }}">
                                    Batal
                                </x-ui.button>
                                <x-ui.button type="submit" variant="primary" size="lg" icon="save">
                                    Simpan Formula
                                </x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </main>

    @push('css')
    <link rel="stylesheet" href="{{ asset('css/components/formula-autocomplete.css') }}">
    @endpush

    @push('scripts')
    <script src="{{ asset('js/components/formula-autocomplete.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatten attributes for autocomplete
            const attributesData = [
                @foreach($attributes as $category => $attrs)
                    @foreach($attrs as $attr)
                        {
                            key: '{{ $attr->key }}',
                            label: '{{ $attr->label }}',
                            type: '{{ $attr->is_computed ? "computed" : "regular" }}'
                        },
                    @endforeach
                @endforeach
                { key: 'PERCENTAGE', label: 'Percentage', type: 'function', snippet: 'PERCENTAGE(a, b)' },
                { key: 'IF', label: 'If Condition', type: 'function', snippet: 'IF(condition, true_val, false_val)' },
                { key: 'ROUND', label: 'Round', type: 'function', snippet: 'ROUND(value, decimals)' },
                { key: 'MIN', label: 'Minimum', type: 'function', snippet: 'MIN(a, b)' },
                { key: 'MAX', label: 'Maximum', type: 'function', snippet: 'MAX(a, b)' },
                { key: 'ABS', label: 'Absolute', type: 'function', snippet: 'ABS(value)' }
            ];

            // Initialize autocomplete on expression textarea
            const expressionInput = document.querySelector('textarea[name="expression"]');
            if(expressionInput) {
                // Ensure ID
                if(!expressionInput.id) expressionInput.id = 'expressionInput';
                new FormulaAutocomplete('expressionInput', attributesData);
            }
        });
    </script>
    @endpush
</x-layouts.app>
