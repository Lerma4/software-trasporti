<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expiration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'deadline',
        'description'
    ];

    protected $casts = [
        'deadline' => 'datetime:d-m-Y',
    ];

    public function truck()
    {
        return $this->belongsTo('App\Models\Truck');
    }
}
