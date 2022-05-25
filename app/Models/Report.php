<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];

    protected $fillable = [
        'truck_id',
        'user_id',
        'text',
        'companyId'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function truck()
    {
        return $this->belongsTo('App\Models\Truck');
    }
}
