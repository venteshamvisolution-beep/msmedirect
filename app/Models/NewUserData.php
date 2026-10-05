<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewUserData extends Model
{
    use HasFactory;

    protected $fillable = [
        'msme_business_data_id',
        'is_bulk_user',
        'organization_name',
        'business_type',
        'type_organization',
        'msme_type',
        'business_category',
        'business_sub_category',
        'specialization',
        'contact_no',
        'email',
        'pincode',
        'city',
        'state',
        'experience',
        'document_no',
        'document_file',
        'state_city',
        'business_size',
        'approve',
        'address',
        'remark'
    ];

    protected $casts = [
        'is_bulk_user' => 'boolean',
        'document_file' => 'array',
        'approve' => 'boolean',
        'business_size' => 'array',
    ];
}
