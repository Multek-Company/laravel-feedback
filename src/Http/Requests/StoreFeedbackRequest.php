<?php

namespace Multek\LaravelFeedback\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'metadata' => ['sometimes', 'array'],
        ];

        $metadataRules = config('feedback.metadata.validation', []);

        foreach ($metadataRules as $key => $rule) {
            $rules["metadata.{$key}"] = $rule;
        }

        return $rules;
    }
}
