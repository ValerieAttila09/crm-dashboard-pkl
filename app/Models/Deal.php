<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Deal extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Otomatis ubah stage menjadi UPPERCASE saat menyimpan ke database PostgreSQL
    protected function stage(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper($value),
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}