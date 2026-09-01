<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MaintenanceRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_id',
        'room_id',
        'customer_id',
        'title',
        'description',
        'priority',
        'status',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
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