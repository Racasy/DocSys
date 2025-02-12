<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentComment extends Model
{
    use HasFactory;

    // protected $table = 'document_comments';

    protected $fillable = [
        'document_id',
        'user_id',
        'comment',
    ];

    /**
     * Relationships
     */

    // A comment is attached to a single document.
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // The user (client or accountant) who posted the comment.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
