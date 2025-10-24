<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    public $timestamps = true;
    protected $fillable = ['user_id', 'identifier', 'identifier_type', 'code', 'verification_type', 'attempts', 'expired_at'];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expired_at->isPast();
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
    }

    public function markAsUsed()
    {
        $this->is_used = true;
        $this->save();
    }

    public function scopeValid($query)
    {
        return $query->where('attempts', '<', 5)->where('expired_at', '>', now())->where('is_used', false);
    }   

    public function scopeByIdentifier($query, $identifier, $identifierType)
    {
        return $query->where('identifier', $identifier)->where('identifier_type', $identifierType);
    }

    public function scopeByVerificationType($query, $verificationType)
    {
        return $query->where('verification_type', $verificationType);
    }
}
