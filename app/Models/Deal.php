<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Deal extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'amount',
        'stage',
        'expected_close_date',
        'customer_id',
        'assigned_to',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}