<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RoomScene extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'room_id',
        'title',
        'image_url',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function hotspots()
    {
        return $this->hasMany(RoomHotspot::class, 'room_scene_id');
    }
}