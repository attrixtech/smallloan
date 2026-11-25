<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\FinancialLog;
use App\Models\GoldRelease;
use App\Models\Loan;
use App\Models\LoanRenewal;
use App\Models\Payment;
use App\Models\PledgedItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExtraDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $staff = User::where('role', 'staff')->first() ?? $admin;

        // Additional Customers
        $extraCustomers = [
            [
                'name' => 'Anand Prabhakar Joshi',
                'mobile' => '9822334455',
                'email' => 'anand.joshi@example.com',
                'address' => '14, Shastri Nagar, Thane West',
                'city' => 'Thane',
                'state' => 'Maharashtra',
                'pincode' => '400606',
                'pan_number' => 'ABCPJ1122K',
                'aadhaar_number' => '554433221100',
                'id_proof_type' => 'Aadhaar',
                'id_proof_number' => '5544 3322 1100',
            ],
            [
                'name' => 'Bhavna Hitesh Mehta',
                'mobile' => '9867554433',
                'email' => 'bhavna.mehta@example.com',
                'address' => 'B-405, Silver Arch, Andheri West',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400053',
                'pan_number' => 'BGHPM3344L',
                'aadhaar_number' => '665544332211',
                'id_proof_type' => 'PAN',
                'id_proof_number' => 'BGHPM3344L',
            ],
            [
                'name' => 'Sandeep Tukaram Deshmukh',
                'mobile' => '9890112244',
                'email' => 'sandeep.d@example.com',
                'address' => 'Row House 7, Kharghar Sector 12',
                'city' => 'Navi Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '410210',
                'pan_number' => 'CDRPD5566M',
                'aadhaar_number' => '776655443322',
                'id_proof_type' => 'Aadhaar',
                'id_proof_number' => '7766 5544 3322',
            ],
            [
                'name' => 'Kavita Rameshwar Gupta',
                'mobile' => '9811223344',
                'email' => 'kavita.gupta@example.com',
                'address' => '22, Kalbadevi Road, Near Cotton Exchange',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400002',
                'pan_number' => 'DEFPG7788N',
                'aadhaar_number' => '887766554433',
                'id_proof_type' => 'Aadhaar',
                'id_proof_number' => '8877 6655 4433',
            ],
            [
                'name' => 'Farhan Salim Shaikh',
                'mobile' => '9820998877',
                'email' => 'farhan.shaikh@example.com',
                'address' => 'Flat 104, Noor Residency, Kurla West',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400070',
                'pan_number' => 'EFGPS9900P',
                'aadhaar_number' => '998877665544',
                'id_proof_type' => 'Driving License',
                'id_proof_number' => 'MH-01-2018009911',
            ],
        ];

        $createdCustomers = [];
        foreach ($extraCustomers as $cData) {
            $cData['customer_code'] = Customer::generateCode();
            $cData['status'] = 'active';
            $createdCustomers[] = Customer::create($cData);
        }

        // Loan 6: Due Today (Active)
        $l6 = Loan::create([
            'loan_no' => Loan::generateLoanNo(),
            'customer_id' => $createdCustomers[0]->id,
            'principal_amount' => 95000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => Carbon::today()->subMonths(6)->toDateString(),
            'maturity_date' => Carbon::today()->toDateString(), // Due Today!
            'tenure_months' => 6,
            'total_pledged_items' => 2,
            'total_gross_weight' => 22.000,
            'total_stone_weight' => 0.800,
            'total_net_weight' => 21.200,
            'total_valuation_amount' => 145220.00,
            'ltv_ratio' => 65.42,
            'total_interest_paid' => 4275.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 95000.00,
            'status' => 'Active',
            'notes' => 'Due today - customer notified for renewal or closure.',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l6->id,
            'customer_id' => $createdCustomers[0]->id,
            'item_name' => '22K Gold Necklace Set',
            'category' => 'Necklace',
            'gross_weight' => 16.000,
            'stone_weight' => 0.800,
            'net_gold_weight' => 15.200,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 104120.00,
            'packet_number' => 'PKT-1008',
            'locker_reference' => 'Locker C - Tray 1',
            'status' => 'Pledged',
        ]);

        PledgedItem::create([
            'loan_id' => $l6->id,
            'customer_id' => $createdCustomers[0]->id,
            'item_name' => '22K Gold Earrings',
            'category' => 'Earring',
            'gross_weight' => 6.000,
            'stone_weight' => 0.000,
            'net_gold_weight' => 6.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 41100.00,
            'packet_number' => 'PKT-1009',
            'locker_reference' => 'Locker C - Tray 1',
            'status' => 'Pledged',
        ]);

        // Today's payment for Loan 6
        Payment::create([
            'payment_no' => Payment::generatePaymentNo(),
            'loan_id' => $l6->id,
            'customer_id' => $createdCustomers[0]->id,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_type' => 'interest_only',
            'interest_amount' => 4275.00,
            'principal_amount' => 0.00,
            'total_amount_paid' => 4275.00,
            'payment_mode' => 'upi',
            'reference_number' => 'UPI/TODAY99182',
            'principal_balance_after' => 95000.00,
            'notes' => '3 months interest collected today',
            'received_by' => $staff->id,
            'is_immutable' => true,
        ]);

        // Loan 7: Active Daily Interest Loan (Bhavna Mehta)
        $l7 = Loan::create([
            'loan_no' => Loan::generateLoanNo(),
            'customer_id' => $createdCustomers[1]->id,
            'principal_amount' => 150000.00,
            'interest_rate' => 1.25,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'daily',
            'issue_date' => Carbon::today()->subDays(45)->toDateString(),
            'maturity_date' => Carbon::today()->addMonths(10)->toDateString(),
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 32.000,
            'total_stone_weight' => 0.000,
            'total_net_weight' => 32.000,
            'total_valuation_amount' => 219200.00,
            'ltv_ratio' => 68.43,
            'total_interest_paid' => 1875.00,
            'total_principal_paid' => 25000.00,
            'current_balance_principal' => 125000.00,
            'status' => 'Active',
            'notes' => 'Daily interest calculation scheme',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l7->id,
            'customer_id' => $createdCustomers[1]->id,
            'item_name' => '22K Heavy Gold Kada (2 pcs)',
            'category' => 'Bangle',
            'gross_weight' => 32.000,
            'stone_weight' => 0.000,
            'net_gold_weight' => 32.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 219200.00,
            'packet_number' => 'PKT-1010',
            'locker_reference' => 'Locker C - Tray 2',
            'status' => 'Pledged',
        ]);

        // Today's partial payment for Loan 7
        Payment::create([
            'payment_no' => Payment::generatePaymentNo(),
            'loan_id' => $l7->id,
            'customer_id' => $createdCustomers[1]->id,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_type' => 'partial_both',
            'interest_amount' => 1875.00,
            'principal_amount' => 25000.00,
            'total_amount_paid' => 26875.00,
            'payment_mode' => 'bank_transfer',
            'reference_number' => 'IMPS/ICICI88271',
            'principal_balance_after' => 125000.00,
            'notes' => 'Part principal + interest paid today',
            'received_by' => $admin->id,
            'is_immutable' => true,
        ]);

        // Loan 8: Overdue Loan (Sandeep Deshmukh)
        $l8 = Loan::create([
            'loan_no' => Loan::generateLoanNo(),
            'customer_id' => $createdCustomers[2]->id,
            'principal_amount' => 60000.00,
            'interest_rate' => 1.75,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => Carbon::today()->subMonths(15)->toDateString(),
            'maturity_date' => Carbon::today()->subMonths(3)->toDateString(),
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 14.500,
            'total_stone_weight' => 0.500,
            'total_net_weight' => 14.000,
            'total_valuation_amount' => 95900.00,
            'ltv_ratio' => 62.57,
            'total_interest_paid' => 5250.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 60000.00,
            'status' => 'Overdue',
            'notes' => '90 days overdue - final notice issued',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l8->id,
            'customer_id' => $createdCustomers[2]->id,
            'item_name' => '22K Gold Chain (24 inch)',
            'category' => 'Chain',
            'gross_weight' => 14.500,
            'stone_weight' => 0.500,
            'net_gold_weight' => 14.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 95900.00,
            'packet_number' => 'PKT-1011',
            'locker_reference' => 'Locker D - Tray 1',
            'status' => 'Pledged',
        ]);

        // Loan 9: Active 18K Loan (Kavita Gupta)
        $l9 = Loan::create([
            'loan_no' => Loan::generateLoanNo(),
            'customer_id' => $createdCustomers[3]->id,
            'principal_amount' => 42000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'reducing_balance',
            'issue_date' => Carbon::today()->subMonths(2)->toDateString(),
            'maturity_date' => Carbon::today()->addMonths(10)->toDateString(),
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 13.000,
            'total_stone_weight' => 1.000,
            'total_net_weight' => 12.000,
            'total_valuation_amount' => 67200.00,
            'ltv_ratio' => 62.50,
            'total_interest_paid' => 630.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 42000.00,
            'status' => 'Active',
            'notes' => '18K diamond-studded ring pledge',
            'created_by' => $staff->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l9->id,
            'customer_id' => $createdCustomers[3]->id,
            'item_name' => '18K Gold Cocktail Ring',
            'category' => 'Ring',
            'gross_weight' => 13.000,
            'stone_weight' => 1.000,
            'net_gold_weight' => 12.000,
            'purity' => '18K',
            'gold_rate_per_gram' => 5600.00,
            'valuation_amount' => 67200.00,
            'packet_number' => 'PKT-1012',
            'locker_reference' => 'Locker D - Tray 2',
            'status' => 'Pledged',
        ]);

        // Loan 10: Closed & Released Loan (Farhan Shaikh)
        $l10 = Loan::create([
            'loan_no' => Loan::generateLoanNo(),
            'customer_id' => $createdCustomers[4]->id,
            'principal_amount' => 70000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => Carbon::today()->subMonths(4)->toDateString(),
            'maturity_date' => Carbon::today()->addMonths(8)->toDateString(),
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 15.000,
            'total_stone_weight' => 0.000,
            'total_net_weight' => 15.000,
            'total_valuation_amount' => 102750.00,
            'ltv_ratio' => 68.13,
            'total_interest_paid' => 4200.00,
            'total_principal_paid' => 70000.00,
            'current_balance_principal' => 0.00,
            'status' => 'Closed',
            'closed_at' => Carbon::today(),
            'settlement_discount' => 0.00,
            'notes' => 'Settled in full today.',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l10->id,
            'customer_id' => $createdCustomers[4]->id,
            'item_name' => '22K Gold Bracelet',
            'category' => 'Bracelet',
            'gross_weight' => 15.000,
            'stone_weight' => 0.000,
            'net_gold_weight' => 15.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 102750.00,
            'packet_number' => 'PKT-1013',
            'locker_reference' => 'Locker D - Tray 3',
            'status' => 'Released',
            'released_at' => Carbon::today(),
            'release_receipt_no' => 'REL-2026-0002',
        ]);

        Payment::create([
            'payment_no' => Payment::generatePaymentNo(),
            'loan_id' => $l10->id,
            'customer_id' => $createdCustomers[4]->id,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_type' => 'full_settlement',
            'interest_amount' => 4200.00,
            'principal_amount' => 70000.00,
            'total_amount_paid' => 74200.00,
            'payment_mode' => 'cash',
            'reference_number' => 'CASH-SETTLE',
            'principal_balance_after' => 0.00,
            'notes' => 'Full settlement paid in cash today',
            'received_by' => $admin->id,
            'is_immutable' => true,
        ]);

        GoldRelease::create([
            'release_no' => GoldRelease::generateReleaseNo(),
            'loan_id' => $l10->id,
            'customer_id' => $createdCustomers[4]->id,
            'release_date' => Carbon::today()->toDateString(),
            'items_count' => 1,
            'total_net_weight' => 15.000,
            'handed_over_to' => 'Farhan Salim Shaikh',
            'recipient_relation' => 'self',
            'recipient_id_proof' => 'DL: MH-01-2018009911',
            'recipient_signature_note' => 'Verified and handed over in good condition',
            'is_authorized_override' => false,
            'processed_by' => $admin->id,
        ]);

        FinancialLog::record('extra_demo_seeded', 'Added 5 more customers, 5 loans, today\'s collections, and release records', 0);
    }
}
