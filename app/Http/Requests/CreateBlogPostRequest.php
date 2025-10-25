<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogPostRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert an empty string for body_raw to null. This allows the 'nullable'
        // rule to pass if a photo is present, while still allowing the
        // 'required_without' rule to fail correctly if no photo is present.
        if ($this->get('body_raw') === '') {
            $this->merge([
                'body_raw' => null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['sometimes', 'required_without:body_raw', 'string'],
            'body_raw' => ['required_without:photo', 'nullable', 'string', 'min:1'],
            'is_published' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'photo' => ['required_without:body_raw', 'nullable', 'image'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}