<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'name',
        'mobile',
        'secondary_phone',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'pan_number',
        'aadhaar_number',
        'id_proof_type',
        'id_proof_number',
        'photo_path',
        'notes',
        'status',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['Active', 'Overdue']);
    }

    public function pledgedItems()
    {
        return $this->hasMany(PledgedItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalOutstandingAttribute(): float
    {
        return (float) $this->activeLoans()->sum('current_balance_principal');
    }

    public function getTotalGoldPledgedGramsAttribute(): float
    {
        return (float) $this->pledgedItems()->where('status', 'Pledged')->sum('net_gold_weight');
    }

    public static function generateCode(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'CUST-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
