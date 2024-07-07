<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';
    protected $fillable = ['user_id', 'full_name', 'email_address', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
