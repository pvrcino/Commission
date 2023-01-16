<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliacao extends Model
{
    public $table = "afiliacoes";
    use HasFactory;

    protected $casts = [
        'subseller' => 'json',
    ];
}
