<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Property extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_id',
        'name',
        'address',
        'description',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}