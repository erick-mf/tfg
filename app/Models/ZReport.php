<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class ZReport extends Model
{
    /** @use HasFactory<\Database\Factories\ZReportFactory> */
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'total_revenue',
        'expected_cash',
        'counted_cash',
        'cash_difference',
        'total_card',
        'total_orders',
        'notes',
    ];

    protected $appends = [
        'formated_end_date',
        'formated_total_revenue',
        'formated_expected_cash',
        'formated_counted_cash',
        'formated_cash_difference',
        'user_name'];

    protected function casts()
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormatedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d-m-Y H:i') : 'No disponible';
    }

    public function getFormatedTotalRevenueAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->total_revenue, 'EUR');
    }

    public function getFormatedExpectedCashAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->expected_cash, 'EUR');
    }

    public function getFormatedCountedCashAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->counted_cash, 'EUR');
    }

    public function getFormatedCashDifferenceAttribute()
    {
        return NumberFormatter::create('es', NumberFormatter::CURRENCY)->formatCurrency($this->cash_difference, 'EUR');
    }

    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'No disponible';
    }
}
