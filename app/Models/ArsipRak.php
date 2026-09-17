<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAHKAN INI
use Illuminate\Database\Eloquent\Model;

class ArsipRak extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}