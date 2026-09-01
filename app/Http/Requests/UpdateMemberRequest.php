<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('member');
        $profile = $user->siswaProfile;

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa_profiles,nisn,' . ($profile->id ?? 'NULL')],
            'kelas' => ['required', 'string', 'max:20'],
            'jurusan' => ['required', 'string', 'max:50'],
            'angkatan' => ['required', 'string', 'max:9'],
        ];
    }
}
