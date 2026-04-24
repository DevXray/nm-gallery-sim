<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
   protected $fillable = [
    'nama_barang',
    'ukuran',
    'harga_sewa',
    'stok',
    'status_barang',
    'foto'   
];

    // Ambil stok per ukuran (dari JSON)
    public function getStokPerUkuranAttribute()
    {
        return json_decode($this->stok, true) ?: [];
    }

    // Set stok per ukuran (ke JSON)
    public function setStokPerUkuranAttribute($value)
    {
        $this->stok = json_encode($value);
    }

    // Kurangi stok untuk ukuran tertentu
    public function kurangiStok($ukuran, $jumlah = 1)
    {
        $stokArray = $this->getStokPerUkuranAttribute();
        if (isset($stokArray[$ukuran]) && $stokArray[$ukuran] >= $jumlah) {
            $stokArray[$ukuran] -= $jumlah;
            $this->stok = json_encode($stokArray);
            $this->save();
            return true;
        }
        return false;
    }
}