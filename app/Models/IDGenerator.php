<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IDGenerator extends Model
{
    use HasFactory;

    public static function generateID() {
        return round(microtime(true) * 1000);  
    }
}
