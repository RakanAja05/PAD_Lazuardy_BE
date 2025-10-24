<?php

namespace App\Models;

use App\Enums\RatingOption;
use App\Enums\RatingOptionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

        protected $fillable = 
    [
        'from_user_id',
        'to_user_id',
        'quality',
        'delivery',
        'attitude',
        'benefit',
        'rate',
        'review',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }



    public function getQualityLabelAttribute()
    {
        return $this->quality ? RatingOptionEnum::from($this->quality)->displayName() : null;
    }

    public function getDeliveryLabelAttribute()
    {
        return $this->delivery ? RatingOptionEnum::from($this->delivery)->displayName() : null;
    }

    public function getAttitudeLabelAttribute()
    {
        return $this->attitude ? RatingOptionEnum::from($this->attitude)->displayName() : null;
    }

    public function getBenefitLabelAttribute()
    {
        return $this->benefit ? RatingOptionEnum::from($this->benefit)->displayName() : null;
    }
}
