<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime:d-m-Y',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
