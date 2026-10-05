<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemesMaster extends Model
{
    use HasFactory;
    protected $table = 'schemes_master';
    protected $fillable = ['name','is_active'];
}
