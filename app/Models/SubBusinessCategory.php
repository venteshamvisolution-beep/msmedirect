<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubBusinessCategory extends Model
{
    use HasFactory;
    protected $table = 'business_sub_category';
    protected $fillable = ['name','category_id'];
}
