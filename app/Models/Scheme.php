<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;
    protected $fillable = [
        'details','title','show_home','home_text','image','category_id'];
    
    
    protected $casts = [
        'details' => 'array'];
}
