<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\AdaptiveRule;

use App\Http\Requests\BaseFormRequest;

final class StoreAdaptiveRuleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id'                 => ['required', 'string', 'unique:adaptive_rules,id'],
            'name'               => ['required', 'string', 'max:255'],
            'recommendation'     => ['nullable', 'string'],
            'priority'           => ['required', 'integer'],
            'actions'            => ['nullable', 'array'],
            'actions.*.id'       => ['required', 'string', 'exists:adaptive_actions,id'],
            'actions.*.metadata' => ['nullable', 'array'],
            'required_fact_ids'  => ['nullable', 'array'],
            'deduced_fact_ids'   => ['nullable', 'array'],
            'facts'              => ['nullable', 'array'],
            'deduced_facts'      => ['nullable', 'array'],
            'is_active'          => ['boolean'],
            'logic'              => ['nullable', 'string'],
        ];
    }
}
