<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // TAMBAHKAN BARIS INI
    protected $fillable = ['key', 'value'];
}
