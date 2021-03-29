<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MaintAlreadyDone extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $table = 'maintalreadydone';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plate',
        'type',
        'km',
        'price',
        'date',
        'garage',
        'notes',
        'companyId'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y',
    ];

    public function setPlateAttribute($value)
    {
        $this->attributes['plate'] = strtoupper($value);
    }

    public function truck()
    {
        return $this->belongsTo('App\Models\Truck', 'plate', 'plate');
    }
}
