<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAlur extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function pengajuan() {
        return $this->belongsTo(Pengajuan::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}