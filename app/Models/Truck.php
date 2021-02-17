<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Truck extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'plate',
        'type',
        'brand',
        'model',
        'km',
        'description',
        'group',
        'companyId'
    ];

    public function setPlateAttribute($value)
    {
        $this->attributes['plate'] = strtoupper($value);
    }

    public function expirations()
    {
        return $this->hasMany('App\Models\Expiration');
    }
}
