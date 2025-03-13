<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => 'string', // Enum sifatida ishlatish uchun
    ];

    // Local Scope for Allowed status
    public function scopeAllowed($query)
    {
        return $query->where('status', 'Allowed');
    }

    // Factory uchun
    protected static function newFactory()
    {
        return \Database\Factories\RecordFactory::new();
    }
}