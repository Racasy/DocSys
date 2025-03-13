<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    // protected $table = 'document_requests'; // By default, Laravel will use the pluralized model name, so it's optional

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    /**
     * Relationships
     */

    // This request belongs to one user (the client).
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // One document request can have many documents uploaded in response.
    public function documents()
    {
        return $this->hasMany(Document::class, 'request_id'); // Match the database column
    }
    
}
