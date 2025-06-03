<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_available',
        'location_id',
    ];

    protected $appends = [
        'location_name',
        'status',
    ];

    public function casts()
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getLocationNameAttribute()
    {
        return $this->location->name ?? 'Sin ubicación';
    }

    public function getStatusAttribute()
    {
        return $this->is_available ? 'Disponible' : 'No disponible';
    }
}
