<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrashPhoto extends Model
{
    use HasFactory;

    protected $table = 'crashes_photos';

    protected $fillable = [
        'filename',
        'crash_id'
    ];
}
