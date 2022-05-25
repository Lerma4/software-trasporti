<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'companyId',
        'user_email',
        'user_name',
        'note',
        'read',
        'author'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'email', 'user_email');
    }

    public function pdf()
    {
        return $this->hasOne('App\Models\DocumentFile');
    }
}
