<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sync_source',
        'fetched_count',
        'added_count',
        'skipped_count',
        'stock_updated_count',
        'deleted_count', // <-- THE FIX: Add this line
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}