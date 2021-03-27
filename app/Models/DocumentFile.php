<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DocumentFile extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'documents_files';

    protected $fillable = [
        'filename',
        'document_id'
    ];

    public function document()
    {
        return $this->belongsTo('App\Models\Document');
    }
}
