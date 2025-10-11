<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory;

    
    protected $fillable = 
    [
        'user_id',
        'name',
        'type',
        'path_url'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
