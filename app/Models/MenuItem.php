<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;

class MenuItem extends Model
{
    /** @use HasFactory<\Database\Factories\MenuItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'is_available',
        'image_path',
        'location',
        'menu_category_id',
    ];

    protected $appends = [
        'status',
        'category_name',
        'image_url',
        'formated_price',
    ];

    protected function casts()
    {
        return [
            'is_available' => 'boolean',
            'price' => 'float',
        ];
    }

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }

    public function getStatusAttribute()
    {
        return $this->is_available ? 'Disponible' : 'No disponible';
    }

    public function getCategoryNameAttribute()
    {
        return $this->menuCategory ? $this->menuCategory->name : 'Sin categoría';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url('img/menu-items/'.$this->image_path);
        }
    }

    public function getFormatedPriceAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->price, 'EUR');
    }
}
