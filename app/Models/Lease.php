<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lease extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_id',
        'room_id',
        'customer_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_rent' => 'decimal:2',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}