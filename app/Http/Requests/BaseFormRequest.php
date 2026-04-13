<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
