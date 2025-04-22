<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_request_id', // Updated field name
        'user_id',
        'comment',
    ];

    public function documentRequest() // Updated relationship
    {
        return $this->belongsTo(DocumentRequest::class, 'document_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}