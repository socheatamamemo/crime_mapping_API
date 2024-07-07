<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrimeType extends Model
{
    use HasFactory;

    protected $table = 'crime_type';
    protected $fillable = ['crime_type_name', 'image'];

    public function typeCrime()
    {
        return $this->hasMany(Crime::class, 'related_to_crime_type_id');
    }

    public function crimeType()
    {
        return $this->hasMany(CrimePending::class, 'related_to_crime_type_id');
    }
}
