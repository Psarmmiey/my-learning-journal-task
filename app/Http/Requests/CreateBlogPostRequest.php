<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['sometimes', 'required_without:body', 'string'],
            'body' => ['sometimes', 'required_without:photo', 'string'],
            'is_published' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'photo' => ['sometimes', 'required', 'image'],
        ];
    }
}
