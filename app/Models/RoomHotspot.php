<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RoomHotspot extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'room_scene_id',
        'target_scene_id',
        'title',
        'label',
        'description',
        'pitch',
        'yaw',
    ];

    protected $casts = [
        'pitch' => 'float',
        'yaw' => 'float',
    ];

    public function scene()
    {
        return $this->belongsTo(RoomScene::class, 'room_scene_id');
    }

    public function targetScene()
    {
        return $this->belongsTo(RoomScene::class, 'target_scene_id');
    }
}