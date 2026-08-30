<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nisn', 'kelas', 'jurusan', 'angkatan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
