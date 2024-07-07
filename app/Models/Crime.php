<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crime extends Model
{
    use HasFactory;

    protected $table = 'crimes';

    protected $fillable = ['description', 'date', 'status', 'longitude', 'latitude', 'address', 'reportedby_user_id', 'approvedby_admin_id', 'crime_type',];

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reportedby_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approvedby_admin_id');
    }

}