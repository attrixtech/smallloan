<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_no',
        'loan_id',
        'customer_id',
        'release_date',
        'items_count',
        'total_net_weight',
        'handed_over_to',
        'recipient_relation',
        'recipient_id_proof',
        'recipient_signature_note',
        'is_authorized_override',
        'override_reason',
        'authorized_by',
        'processed_by',
    ];

    protected $casts = [
        'release_date' => 'date',
        'total_net_weight' => 'decimal:3',
        'is_authorized_override' => 'boolean',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public static function generateReleaseNo(): string
    {
        $year = date('Y');
        $last = static::where('release_no', 'like', "REL-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match("/REL-{$year}-(\d+)/", $last->release_no, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        return sprintf("REL-%s-%04d", $year, $seq);
    }
}
