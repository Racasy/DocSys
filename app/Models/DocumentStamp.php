<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStamp extends Model
{
    protected $fillable = [
        'document_id',
        'debit_account', 
        'credit_account', 
        'amount',
        'stamp_index',
        'stamped_by', 
        'stamped_at'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stamped_by');
    }
}
