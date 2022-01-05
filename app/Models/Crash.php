<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Crash extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'date',
        'email',
        'name',
        'plate',
        'plate_s',
        'description',
        'companyId',
        'read'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y',
    ];

    function photos()
    {
        return $this->hasMany('App\Models\CrashPhoto');
    }
}
