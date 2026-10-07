<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;
    protected $table = 'user_data';

    protected $fillable = [
        'msme_business_data_id',
        'is_bulk_user',
        'organization_name',
        'company',
        'person_name',
        'business_type',
        'type_organization',
        'msme_type',
        'others',
        'business_category',
        'business_sub_category',
        'specialization',
        'contact_no',
        'office_no',
        'mobile_no',
        'email',
        'website',
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
        'details',
        'business_description',
        'want_to',
        'remark'
    ];

    protected $casts = [
        'is_bulk_user' => 'boolean',
        'document_file' => 'array',
        'approve' => 'boolean',
        'business_size' => 'array',
    ];
}
