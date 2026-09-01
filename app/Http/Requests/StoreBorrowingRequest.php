<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nisn' => ['required', 'string', 'exists:siswa_profiles,nisn'],
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['exists:books,id'],
        ];
    }
}
