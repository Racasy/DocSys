<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'sent_at',
        'read_at',
    ];

    /**
     * Relationships
     */

    // Each notification belongs to a single user (the recipient).
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
