<?php

namespace App\Services;

use App\Models\Loan;
use Carbon\Carbon;

class InterestCalculator
{
    /**
     * Calculate accrued interest for a loan up to a given date.
     *
     * @param Loan $loan
     * @param string|Carbon|null $asOfDate
     * @return array
     */
    public static function calculate(Loan $loan, $asOfDate = null): array
    {
        $targetDate = $asOfDate ? Carbon::parse($asOfDate) : Carbon::today();
        $startDate = Carbon::parse($loan->issue_date);

        // If renewed, start date for unbilled interest can be the renewal date
        $latestRenewal = $loan->renewals()->latest('renewal_date')->first();
        if ($latestRenewal && Carbon::parse($latestRenewal->renewal_date)->gt($startDate)) {
            $startDate = Carbon::parse($latestRenewal->renewal_date);
        }

        if ($targetDate->lt($startDate)) {
            $days = 0;
        } else {
            $days = $startDate->diffInDays($targetDate);
        }

        $principal = (float) $loan->current_balance_principal;
        $monthlyRate = (float) $loan->interest_rate;
        if ($loan->interest_rate_type === 'per_annum') {
            $monthlyRate = $monthlyRate / 12;
        }

        $method = $loan->interest_calculation_method ?? 'monthly';
        $grossInterestAccrued = 0;

        switch ($method) {
            case 'daily':
                // Daily rate = monthly rate / 30
                $dailyRate = $monthlyRate / 30 / 100;
                $grossInterestAccrued = $principal * $dailyRate * $days;
                break;

            case 'monthly':
                // Minimum 1 month interest rule common in Girvi, or proportional after 1 month
                $months = max(1, ceil($days / 30));
                $grossInterestAccrued = $principal * ($monthlyRate / 100) * $months;
                break;

            case 'annual':
                $annualRate = ($loan->interest_rate_type === 'per_annum') ? (float)$loan->interest_rate : ($monthlyRate * 12);
                $grossInterestAccrued = $principal * ($annualRate / 100) * ($days / 365);
                break;

            case 'flat':
                // Flat tenure interest based on tenure_months
                $months = $loan->tenure_months > 0 ? $loan->tenure_months : 12;
                $grossInterestAccrued = $principal * ($monthlyRate / 100) * $months;
                break;

            case 'reducing_balance':
            default:
                // Reducing balance on current remaining principal for actual days or months
                $monthsPassed = max(1, $days / 30);
                $grossInterestAccrued = $principal * ($monthlyRate / 100) * $monthsPassed;
                break;
        }

        $grossInterestAccrued = round($grossInterestAccrued, 2);
        $interestPaid = (float) $loan->total_interest_paid;
        
        // If there was a renewal, total_interest_paid might include previous cleared interest.
        // For current settlement:
        $netInterestDue = max(0, round($grossInterestAccrued - $interestPaid, 2));

        // In case previous payments cleared all accrued or more:
        if ($grossInterestAccrued <= $interestPaid) {
            $netInterestDue = 0.00;
        }

        $principalDue = round($principal, 2);
        $totalSettlement = round($principalDue + $netInterestDue, 2);

        $tenureMonths = max(1, (int) ($loan->tenure_months ?? 12));
        $emiData = self::calculateEmi((float) $loan->principal_amount, $monthlyRate, $tenureMonths, $method, $principalDue);

        return [
            'days' => $days,
            'months' => round($days / 30, 1),
            'principal' => $principalDue,
            'monthly_rate_pct' => $monthlyRate,
            'calculation_method' => $method,
            'gross_interest_accrued' => $grossInterestAccrued,
            'interest_paid' => $interestPaid,
            'net_interest_due' => $netInterestDue,
            'total_settlement_amount' => $totalSettlement,
            'monthly_emi' => $emiData['emi'],
            'monthly_emi_interest' => $emiData['interest_component'],
            'monthly_emi_principal' => $emiData['principal_component'],
            'is_overdue' => Carbon::today()->gt(Carbon::parse($loan->maturity_date)) && $loan->status !== 'Closed',
        ];
    }

    /**
     * Calculate Monthly EMI (Principal + Interest) for a loan.
     */
    public static function calculateEmi(float $originalPrincipal, float $monthlyRatePct, int $tenureMonths, string $method = 'monthly', ?float $currentBalance = null): array
    {
        $n = max(1, $tenureMonths);
        $r = max(0, $monthlyRatePct) / 100;
        $balance = $currentBalance !== null ? $currentBalance : $originalPrincipal;

        if ($originalPrincipal <= 0) {
            return ['emi' => 0.0, 'interest_component' => 0.0, 'principal_component' => 0.0];
        }

        if ($r == 0.0) {
            $emi = round($originalPrincipal / $n, 2);
            $principalComp = min($balance, $emi);
            return [
                'emi' => $emi,
                'interest_component' => 0.0,
                'principal_component' => round($principalComp, 2),
            ];
        }

        if ($method === 'flat') {
            $interestComp = round($originalPrincipal * $r, 2);
            $principalComp = round($originalPrincipal / $n, 2);
            $emi = round($interestComp + $principalComp, 2);
            $principalComp = min($balance, $principalComp);
        } else {
            // Standard reducing/amortized Monthly EMI: P * r * (1+r)^n / ((1+r)^n - 1)
            $factor = pow(1 + $r, $n);
            $emi = round($originalPrincipal * ($r * $factor) / ($factor - 1), 2);
            $interestComp = round($balance * $r, 2);
            $principalComp = round(min($balance, max(0, $emi - $interestComp)), 2);
        }

        return [
            'emi' => $emi,
            'interest_component' => $interestComp,
            'principal_component' => $principalComp,
        ];
    }
}
