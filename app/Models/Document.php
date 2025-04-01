<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'request_id',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'uploaded_at',
    ];

    /**
     * Relationships
     */

    // The user (client) who uploaded this document.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The specific document request this file is fulfilling.
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
    }

    // A document can have many comments (feedback from accountants/clients).
    public function comments()
    {
        return $this->hasMany(DocumentComment::class);
    }

    public function stamps()
    {
        return $this->hasMany(DocumentStamp::class);
    }

    public function hasMaxStamps($maxStamps = 5)
    {
        return $this->stamps()->count() >= $maxStamps;
    }

}
