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
        'issue_image_url',
        'completion_image_url',
        'priority',
        'status',
        'cost',
    ];

    // Relasi ke Kamar
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relasi ke Pelapor / Customer (Penyewa)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}