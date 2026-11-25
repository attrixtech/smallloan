<?php

namespace App\Models;

use App\Services\InterestCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_no',
        'customer_id',
        'principal_amount',
        'interest_rate',
        'interest_rate_type',
        'interest_calculation_method',
        'issue_date',
        'maturity_date',
        'tenure_months',
        'total_pledged_items',
        'total_gross_weight',
        'total_stone_weight',
        'total_net_weight',
        'total_valuation_amount',
        'ltv_ratio',
        'total_interest_paid',
        'total_principal_paid',
        'current_balance_principal',
        'status',
        'closed_at',
        'settlement_discount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'maturity_date' => 'date',
        'closed_at' => 'datetime',
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_gross_weight' => 'decimal:3',
        'total_stone_weight' => 'decimal:3',
        'total_net_weight' => 'decimal:3',
        'total_valuation_amount' => 'decimal:2',
        'ltv_ratio' => 'decimal:2',
        'total_interest_paid' => 'decimal:2',
        'total_principal_paid' => 'decimal:2',
        'current_balance_principal' => 'decimal:2',
        'settlement_discount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pledgedItems()
    {
        return $this->hasMany(PledgedItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'asc');
    }

    public function renewals()
    {
        return $this->hasMany(LoanRenewal::class)->orderBy('renewal_date', 'desc');
    }

    public function goldReleases()
    {
        return $this->hasMany(GoldRelease::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getInterestCalculationAttribute(): array
    {
        return InterestCalculator::calculate($this);
    }

    public function checkAndUpdateOverdueStatus(): void
    {
        if ($this->status === 'Active' && Carbon::today()->gt(Carbon::parse($this->maturity_date))) {
            $this->status = 'Overdue';
            $this->saveQuietly();
        } elseif ($this->status === 'Overdue' && Carbon::today()->lte(Carbon::parse($this->maturity_date))) {
            $this->status = 'Active';
            $this->saveQuietly();
        }
    }

    public static function generateLoanNo(): string
    {
        $year = date('Y');
        $last = static::withTrashed()
            ->where('loan_no', 'like', "LN-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match("/LN-{$year}-(\d+)/", $last->loan_no, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        return sprintf("LN-%s-%04d", $year, $seq);
    }
}
