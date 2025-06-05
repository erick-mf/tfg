<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'total',
        'status',
        'payment_method',
        'paid_at',
        'user_id',
        'table_id',
    ];

    protected $appends = [
        'formated_total',
        'formated_paid_at',
        'user_name',
        'table_number',
    ];

    public function casts()
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // NOTE: no se puede usar 'table' como nombre de la relación, da error
    public function assignedTable()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function getFormatedTotalAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->total, 'EUR');
    }

    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'No disponible';
    }

    public function getTableNumberAttribute()
    {
        return $this->assignedTable->name;
    }

    public function getFormatedPaidAtAttribute()
    {
        return $this->paid_at ? $this->paid_at->format('d/m/Y H:i') : 'Todavía no pagado';
    }
}
