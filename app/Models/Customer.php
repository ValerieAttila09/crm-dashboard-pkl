<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'status',
        'created_by',
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}