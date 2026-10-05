<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessEmailCampaignItem extends Model
{
    use HasFactory;

    protected $table = 'business_email_campaign_items';

    protected $fillable = [
        'campaign_id',
        'business_data_id',
        'email',
        'message_id',
        'status',
        'delivery_status',
        'attempts',
        'sent_at',
        'delivered_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(BusinessEmailCampaign::class, 'campaign_id');
    }

    public function businessData(): BelongsTo
    {
        return $this->belongsTo(MsmeBusinessData::class, 'business_data_id');
    }
}
