<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_email',
        'name',
        'plate',
        'type',
        'plate_s',
        'container',
        'garage',
        'start',
        'destination',
        'stops',
        'km',
        'distance',
        'petrol_station',
        'fuel',
        'cost',
        'note',
        'companyId',
        'created_at'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y / H:m',
    ];

    public function user_email()
    {
        return $this->belongsTo('App\Models\User', 'email', 'user_email');
    }

    public function user_name()
    {
        return $this->belongsTo('App\Models\User', 'name', 'name');
    }
}
