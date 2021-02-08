<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Maintenance extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plate',
        'type',
        'km',
        'description',
        'period',
        'price',
        'alert',
        'date',
        'garage',
        'companyId'
    ];

    public function truck()
    {
        return $this->belongsTo('App\Models\Truck', 'plate', 'plate');
    }
}
