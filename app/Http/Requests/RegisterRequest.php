<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa_profiles,nisn'],
            'kelas' => ['required', 'string', 'max:20'],
            'jurusan' => ['required', 'string', 'max:50'],
            'angkatan' => ['required', 'string', 'max:9'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
