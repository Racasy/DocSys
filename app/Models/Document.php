<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_id',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'uploaded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
    }

    public function stamps()
    {
        return $this->hasMany(DocumentStamp::class);
    }

    public function hasMaxStamps($maxStamps = 5)
    {
        return $this->stamps()->count() >= $maxStamps;
    }

    // public function comments()
    // {
    //     return $this->hasMany(DocumentComment::class);
    // }
}