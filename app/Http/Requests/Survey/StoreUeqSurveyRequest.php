<?php

namespace App\Http\Requests\Survey;

use App\Http\Requests\BaseFormRequest;

class StoreUeqSurveyRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nim'   => 'required|string|max:20',
            'class' => 'required|string|max:20',

            'annoying_enjoyable'                => 'required|integer|between:1,7',
            'not_understandable_understandable' => 'required|integer|between:1,7',
            'creative_dull'                     => 'required|integer|between:1,7',
            'easy_difficult'                    => 'required|integer|between:1,7',
            'valuable_inferior'                 => 'required|integer|between:1,7',
            'boring_exciting'                   => 'required|integer|between:1,7',
            'not_interesting_interesting'       => 'required|integer|between:1,7',
            'unpredictable_predictable'         => 'required|integer|between:1,7',
            'fast_slow'                         => 'required|integer|between:1,7',
            'inventive_conventional'            => 'required|integer|between:1,7',
            'obstructive_supportive'            => 'required|integer|between:1,7',
            'good_bad'                          => 'required|integer|between:1,7',
            'complicated_easy'                  => 'required|integer|between:1,7',
            'unlikable_pleasing'                => 'required|integer|between:1,7',
            'usual_leading_edge'                => 'required|integer|between:1,7',
            'unpleasant_pleasant'               => 'required|integer|between:1,7',
            'secure_not_secure'                 => 'required|integer|between:1,7',
            'motivating_demotivating'           => 'required|integer|between:1,7',
            'meets_expectations_does_not_meet'  => 'required|integer|between:1,7',
            'inefficient_efficient'             => 'required|integer|between:1,7',
            'clear_confusing'                   => 'required|integer|between:1,7',
            'impractical_practical'             => 'required|integer|between:1,7',
            'organized_cluttered'               => 'required|integer|between:1,7',
            'attractive_unattractive'           => 'required|integer|between:1,7',
            'friendly_unfriendly'               => 'required|integer|between:1,7',
            'conservative_innovative'           => 'required|integer|between:1,7',

            'comments'    => 'required|max:1000',
            'suggestions' => 'required|max:1000',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $between = 'Skala penilaian harus bernilai antara 1 sampai 7';

        return array_merge(parent::messages(), [
            'annoying_enjoyable.required'                => 'Skala penilaian antara Menyebalkan-Menyenangkan wajib diisi',
            'not_understandable_understandable.required' => 'Skala penilaian antara Tidak dapat dipahami-Dapat dipahami wajib diisi',
            'creative_dull.required'                     => 'Skala penilaian antara Kreatif-Monoton wajib diisi',
            'easy_difficult.required'                    => 'Skala penilaian antara Mudah-Sulit wajib diisi',
            'valuable_inferior.required'                 => 'Skala penilaian antara Bermanfaat-Kurang bermanfaat wajib diisi',
            'boring_exciting.required'                   => 'Skala penilaian antara Membosankan-Menarik wajib diisi',
            'not_interesting_interesting.required'       => 'Skala penilaian antara Tidak menarik-Menarik wajib diisi',
            'unpredictable_predictable.required'         => 'Skala penilaian antara Tidak dapat diprediksi-Dapat diprediksi wajib diisi',
            'fast_slow.required'                         => 'Skala penilaian antara Cepat-Lambat wajib diisi',
            'inventive_conventional.required'            => 'Skala penilaian antara Inovatif-Konvensional wajib diisi',
            'obstructive_supportive.required'            => 'Skala penilaian antara Menghambat-Mendukung wajib diisi',
            'good_bad.required'                          => 'Skala penilaian antara Baik-Buruk wajib diisi',
            'complicated_easy.required'                  => 'Skala penilaian antara Rumit-Sederhana wajib diisi',
            'unlikable_pleasing.required'                => 'Skala penilaian antara Tidak disukai-Menyenangkan wajib diisi',
            'usual_leading_edge.required'                => 'Skala penilaian antara Biasa saja-Terdepan wajib diisi',
            'unpleasant_pleasant.required'               => 'Skala penilaian antara Tidak menyenangkan-Menyenangkan wajib diisi',
            'secure_not_secure.required'                 => 'Skala penilaian antara Aman-Tidak aman wajib diisi',
            'motivating_demotivating.required'           => 'Skala penilaian antara Memotivasi-Tidak memotivasi wajib diisi',
            'meets_expectations_does_not_meet.required'  => 'Skala penilaian antara Memenuhi ekspektasi-Tidak memenuhi ekspektasi wajib diisi',
            'inefficient_efficient.required'             => 'Skala penilaian antara Tidak efisien-Efisien wajib diisi',
            'clear_confusing.required'                   => 'Skala penilaian antara Jelas-Membingungkan wajib diisi',
            'impractical_practical.required'             => 'Skala penilaian antara Tidak praktis-Praktis wajib diisi',
            'organized_cluttered.required'               => 'Skala penilaian antara Terorganisir-Berantakan wajib diisi',
            'attractive_unattractive.required'           => 'Skala penilaian antara Menarik-Tidak menarik wajib diisi',
            'friendly_unfriendly.required'               => 'Skala penilaian antara Ramah-Tidak ramah wajib diisi',
            'conservative_innovative.required'           => 'Skala penilaian antara Konservatif-Inovatif wajib diisi',

            'annoying_enjoyable.between'                => $between,
            'not_understandable_understandable.between' => $between,
            'creative_dull.between'                     => $between,
            'easy_difficult.between'                    => $between,
            'valuable_inferior.between'                 => $between,
            'boring_exciting.between'                   => $between,
            'not_interesting_interesting.between'       => $between,
            'unpredictable_predictable.between'         => $between,
            'fast_slow.between'                         => $between,
            'inventive_conventional.between'            => $between,
            'obstructive_supportive.between'            => $between,
            'good_bad.between'                          => $between,
            'complicated_easy.between'                  => $between,
            'unlikable_pleasing.between'                => $between,
            'usual_leading_edge.between'                => $between,
            'unpleasant_pleasant.between'               => $between,
            'secure_not_secure.between'                 => $between,
            'motivating_demotivating.between'           => $between,
            'meets_expectations_does_not_meet.between'  => $between,
            'inefficient_efficient.between'             => $between,
            'clear_confusing.between'                   => $between,
            'impractical_practical.between'             => $between,
            'organized_cluttered.between'               => $between,
            'attractive_unattractive.between'           => $between,
            'friendly_unfriendly.between'               => $between,
            'conservative_innovative.between'           => $between,

            'comments.required'    => 'Komentar wajib diisi',
            'suggestions.required' => 'Saran wajib diisi',
            'comments.max'         => 'Komentar tidak boleh lebih dari 1000 karakter',
            'suggestions.max'      => 'Saran tidak boleh lebih dari 1000 karakter',
            'nim.required'         => 'NIM wajib diisi',
            'class.required'       => 'Kelas wajib diisi',
        ]);
    }
}
