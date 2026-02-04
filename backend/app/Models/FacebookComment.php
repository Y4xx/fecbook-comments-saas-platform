<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FacebookComment extends Model
{
    protected $fillable = [
        'facebook_page_id',
        'facebook_comment_id',
        'post_id',
        'message',
        'author_name',
        'author_id',
        'comment_created_time',
        'sentiment_status',
    ];

    protected $casts = [
        'comment_created_time' => 'datetime',
    ];

    public function facebookPage(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class);
    }

    public function sentimentResult(): HasOne
    {
        return $this->hasOne(SentimentResult::class);
    }
}
