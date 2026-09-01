<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'synopsis' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'author_id' => ['required', 'exists:authors,id'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'rack_id' => ['nullable', 'exists:racks,id'],
            'published_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
