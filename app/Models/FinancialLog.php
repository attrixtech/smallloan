<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinancialLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event_type',
        'reference_no',
        'customer_id',
        'loan_id',
        'amount',
        'description',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public static function record(string $eventType, string $description, float $amount = 0, ?int $loanId = null, ?int $customerId = null, ?string $referenceNo = null, ?array $metadata = null): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'reference_no' => $referenceNo,
            'customer_id' => $customerId,
            'loan_id' => $loanId,
            'amount' => $amount,
            'description' => $description,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
