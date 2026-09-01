<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    // Matikan pembaruan kolom updated_at
    const UPDATED_AT = null;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}