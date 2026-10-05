<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessEmailCampaign extends Model
{
    use HasFactory;

    protected $table = 'business_email_campaigns';

    protected $fillable = [
        'parent_id',
        'start_id',
        'end_id',
        'batch_size',
        'max_id',
        'name',
        'subject',
        'content',
        'total_count',
        'skipped_count',
        'queued_count',
        'sent_count',
        'delivered_count',
        'failed_count',
        'pending_count',
        'status',
        'started_at',
        'completed_at',
        'error_log',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BusinessEmailCampaignItem::class, 'campaign_id');
    }

    public function parent()
    {
        return $this->belongsTo(BusinessEmailCampaign::class, 'parent_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(BusinessEmailCampaign::class, 'parent_id');
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->queued_count <= 0) {
            return $this->status === 'completed' ? 100.0 : 0.0;
        }

        return round(($this->sent_count / $this->queued_count) * 100, 2);
    }
}
