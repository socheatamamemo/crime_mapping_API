<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrimePending extends Model
{
    use HasFactory;
    
    protected $table = 'pending_crimes'; 
    protected $fillable = [
        'description',
        'date',
        'status',
        'longitude',
        'latitude',
        'address',
        'reportedby_user_id',
        'crime_type',
    ];

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reportedby_user_id');
    }
}