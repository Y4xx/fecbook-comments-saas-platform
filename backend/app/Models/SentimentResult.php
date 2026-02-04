<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentResult extends Model
{
    protected $fillable = [
        'facebook_comment_id',
        'sentiment',
        'confidence',
        'reason',
    ];

    protected $casts = [
        'confidence' => 'float',
    ];

    public function facebookComment(): BelongsTo
    {
        return $this->belongsTo(FacebookComment::class);
    }
}
