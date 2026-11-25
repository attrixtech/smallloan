<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PledgedItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'customer_id',
        'item_name',
        'category',
        'gross_weight',
        'stone_weight',
        'net_gold_weight',
        'purity',
        'gold_rate_per_gram',
        'valuation_amount',
        'packet_number',
        'locker_reference',
        'photo_path',
        'status',
        'released_at',
        'release_receipt_no',
        'remarks',
    ];

    protected $casts = [
        'gross_weight' => 'decimal:3',
        'stone_weight' => 'decimal:3',
        'net_gold_weight' => 'decimal:3',
        'gold_rate_per_gram' => 'decimal:2',
        'valuation_amount' => 'decimal:2',
        'released_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
