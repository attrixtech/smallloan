<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'renewal_no',
        'loan_id',
        'customer_id',
        'previous_maturity_date',
        'new_maturity_date',
        'renewal_date',
        'interest_cleared_amount',
        'payment_id',
        'principal_adjusted',
        'new_principal_amount',
        'renewal_fee',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'previous_maturity_date' => 'date',
        'new_maturity_date' => 'date',
        'renewal_date' => 'date',
        'interest_cleared_amount' => 'decimal:2',
        'principal_adjusted' => 'decimal:2',
        'new_principal_amount' => 'decimal:2',
        'renewal_fee' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function generateRenewalNo(): string
    {
        $year = date('Y');
        $last = static::where('renewal_no', 'like', "RNW-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match("/RNW-{$year}-(\d+)/", $last->renewal_no, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        return sprintf("RNW-%s-%04d", $year, $seq);
    }
}
