<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function riwayat() {
        return $this->hasMany(RiwayatAlur::class);
    }

    public function userLoket() {
        return $this->belongsTo(User::class, 'user_loket_id');
    }

    public function latestRiwayat()
    {
        return $this->hasOne(RiwayatAlur::class)->latestOfMany();
    }

        /**
     * TAMBAHKAN BLOK INI
     * Memberi tahu Laravel untuk memperlakukan kolom ini sebagai objek tanggal.
     */
    protected $casts = [
        'tenggat_waktu' => 'datetime',
        'original_tenggat_waktu' => 'datetime',
    ];
}
