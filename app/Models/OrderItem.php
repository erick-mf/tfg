<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'quantity',
        'unit_price',
        'subtotal',
        'status',
        'notes',
        'order_id',
        'menu_item_id',
    ];

    protected $appends = [
        'menu_item_name',
        'formated_unit_price',
        'formated_subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function getMenuItemNameAttribute()
    {
        return $this->menuItem->name;
    }

    public function getFormatedUnitPriceAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->unit_price, 'EUR');
    }

    public function getFormatedSubtotalAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->subtotal, 'EUR');
    }
}
