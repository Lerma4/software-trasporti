<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crash extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'email',
        'name',
        'plate',
        'plate_s',
        'description',
        'companyId'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y',
    ];

    function photos()
    {
        return $this->hasMany('App\Models\CrashPhoto');
    }
}
