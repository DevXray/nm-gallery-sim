<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilToko extends Model
{
    use HasFactory;

    protected $table = 'profil_toko';

    protected $fillable = [
        'nama_toko',
        'pemilik',
        'telepon',
        'alamat',
        'instagram',
        'logo'
    ];
}