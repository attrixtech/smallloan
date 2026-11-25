<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_no',
        'loan_id',
        'customer_id',
        'payment_date',
        'payment_type',
        'interest_amount',
        'principal_amount',
        'penalty_amount',
        'discount_amount',
        'total_amount_paid',
        'payment_mode',
        'reference_number',
        'principal_balance_after',
        'notes',
        'received_by',
        'is_immutable',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'interest_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount_paid' => 'decimal:2',
        'principal_balance_after' => 'decimal:2',
        'is_immutable' => 'boolean',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public static function generatePaymentNo(): string
    {
        $year = date('Y');
        $last = static::where('payment_no', 'like', "REC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match("/REC-{$year}-(\d+)/", $last->payment_no, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        return sprintf("REC-%s-%04d", $year, $seq);
    }
}
