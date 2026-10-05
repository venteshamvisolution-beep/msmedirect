<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\BusinessType;


class Connections extends Model
{
    use HasFactory;
    protected $table = 'connections';
    
    protected $casts = [
        'timeline' => 'array'];
    
    public function userTableDataFrom()
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }
    
    public function userTableDataConn()
    {
        return $this->belongsTo(User::class, 'conn_id', 'id');
    }
    
    public function businessTypeConn()
    {
        return $this->belongsTo(BusinessType::class, 'conn_business_type_id', 'business_id');
    }
    
    public function businessTypeFrom()
    {
        return $this->belongsTo(BusinessType::class, 'from_business_type_id', 'business_id');
    }
}
