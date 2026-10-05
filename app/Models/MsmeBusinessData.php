<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MsmeBusinessData extends Model
{
    use HasFactory;

    // protected $table = 'msme_business_data';
    protected $table = 'msme_business_local';

    public $timestamps = false;

    protected $fillable = [
        's_no',
        'company',
        'person_name',
        'add',
        'city',
        'pincode',
        'state',
        'office_no',
        'mobile_no',
        'email',
        'website',
        'details',
    ];

    public function campaignItems(): HasMany
    {
        return $this->hasMany(BusinessEmailCampaignItem::class, 'business_data_id');
    }

    public function latestCampaignItem(): HasOne
    {
        return $this->hasOne(BusinessEmailCampaignItem::class, 'business_data_id')->latestOfMany();
    }

    public function userData(): HasOne
    {
        return $this->hasOne(UserData::class, 'email', 'email');
    }
}
