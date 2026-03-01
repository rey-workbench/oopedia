<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base FormRequest providing common authorization and helper utilities.
 *
 * All application FormRequests should extend this class to ensure
 * consistent authorization and message handling across the application.
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Override in child classes to add specific authorization logic.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get common validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required'  => ':attribute wajib diisi.',
            'string'    => ':attribute harus berupa teks.',
            'integer'   => ':attribute harus berupa angka.',
            'boolean'   => ':attribute harus berupa true atau false.',
            'array'     => ':attribute harus berupa array.',
            'email'     => ':attribute harus berupa alamat email yang valid.',
            'max'       => ':attribute tidak boleh lebih dari :max karakter.',
            'min'       => ':attribute harus minimal :min karakter.',
            'unique'    => ':attribute sudah digunakan.',
            'exists'    => ':attribute yang dipilih tidak valid.',
            'in'        => ':attribute yang dipilih tidak valid.',
            'image'     => ':attribute harus berupa file gambar.',
            'mimes'     => ':attribute harus berformat: :values.',
            'nullable'  => ':attribute boleh kosong.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
        ];
    }
}
