<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
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
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@webotapp.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('admin123'),
                'mobile' => '9820011223',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@webotapp.com'],
            [
                'name' => 'Sanjay Verma (Cashier)',
                'password' => Hash::make('staff123'),
                'mobile' => '9820099887',
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 2. Business Settings
        $settings = [
            'shop_name' => 'WeBotApp Gold Loan & Girvi',
            'shop_address' => 'Shop #12, Zaveri Bazaar, Kalbadevi, Mumbai - 400002',
            'phone_number' => '+91 98765 43210',
            'email' => 'contact@webotapp-girvi.com',
            'currency' => 'INR',
            'receipt_header' => 'Trusted Gold Loan & Girvi Financing since 1998',
            'receipt_footer' => 'Subject to Mumbai Jurisdiction. Safe vault custody guaranteed.',
            'default_interest_rate' => '1.50',
            'default_interest_method' => 'monthly',
            'default_loan_tenure_months' => '12',
            'gold_rate_24k' => '7450',
            'gold_rate_22k' => '6850',
            'gold_rate_20k' => '6250',
            'gold_rate_18k' => '5600',
        ];

        foreach ($settings as $key => $val) {
            BusinessSetting::set($key, $val);
        }

        // 3. Customers
        $c1 = Customer::create([
            'customer_code' => 'CUST-0001',
            'name' => 'Rajesh Kumar Sharma',
            'mobile' => '9820123456',
            'secondary_phone' => '9820123457',
            'email' => 'rajesh.sharma@example.com',
            'address' => 'Flat 302, Sai Kripa Heights, Borivali West',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400092',
            'pan_number' => 'ABCPS1234F',
            'aadhaar_number' => '453289012345',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '4532 8901 2345',
            'status' => 'active',
        ]);

        $c2 = Customer::create([
            'customer_code' => 'CUST-0002',
            'name' => 'Sunita Ashok Patil',
            'mobile' => '9833445566',
            'email' => 'sunita.patil@example.com',
            'address' => 'Room 14, Chawl No 3, Dadar East',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400014',
            'pan_number' => 'AYYPP5678K',
            'aadhaar_number' => '789012345678',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '7890 1234 5678',
            'status' => 'active',
        ]);

        $c3 = Customer::create([
            'customer_code' => 'CUST-0003',
            'name' => 'Mohammed Aslam Merchant',
            'mobile' => '9819876543',
            'address' => 'B-12, Green Terrace, Bhendi Bazaar',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400003',
            'pan_number' => 'BJKPM4321Q',
            'aadhaar_number' => '901234567890',
            'id_proof_type' => 'PAN',
            'id_proof_number' => 'BJKPM4321Q',
            'status' => 'active',
        ]);

        $c4 = Customer::create([
            'customer_code' => 'CUST-0004',
            'name' => 'Priya Nilesh Shah',
            'mobile' => '9821098765',
            'address' => '101, Mahavir Darshan, Ghatkopar East',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400077',
            'pan_number' => 'BKVPS9988D',
            'aadhaar_number' => '321098765432',
            'id_proof_type' => 'Aadhaar',
            'id_proof_number' => '3210 9876 5432',
            'status' => 'active',
        ]);

        $c5 = Customer::create([
            'customer_code' => 'CUST-0005',
            'name' => 'Vijay Anand Kadam',
            'mobile' => '9870112233',
            'address' => 'Plot 45, Sector 19, Vashi',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400703',
            'pan_number' => 'CPKPK7766A',
            'aadhaar_number' => '654321098765',
            'id_proof_type' => 'Driving License',
            'id_proof_number' => 'MH-03-2015001234',
            'status' => 'active',
        ]);

        // 4. Loans & Collateral Items
        // LOAN 1: Active Loan (Rajesh Sharma)
        $l1Issue = Carbon::today()->subMonths(3)->toDateString();
        $l1Maturity = Carbon::today()->addMonths(9)->toDateString();
        $l1 = Loan::create([
            'loan_no' => 'LN-2026-0001',
            'customer_id' => $c1->id,
            'principal_amount' => 120000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => $l1Issue,
            'maturity_date' => $l1Maturity,
            'tenure_months' => 12,
            'total_pledged_items' => 2,
            'total_gross_weight' => 28.500,
            'total_stone_weight' => 1.200,
            'total_net_weight' => 27.300,
            'total_valuation_amount' => 187005.00,
            'ltv_ratio' => 64.17,
            'total_interest_paid' => 3600.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 120000.00,
            'status' => 'Active',
            'notes' => 'Customer is a regular businessman in Zaveri Bazaar',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l1->id,
            'customer_id' => $c1->id,
            'item_name' => '22K Gold Bangles (2 pcs)',
            'category' => 'Bangle',
            'gross_weight' => 16.500,
            'stone_weight' => 0.000,
            'net_gold_weight' => 16.500,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 113025.00,
            'packet_number' => 'PKT-1001',
            'locker_reference' => 'Locker A - Tray 1',
            'status' => 'Pledged',
        ]);

        PledgedItem::create([
            'loan_id' => $l1->id,
            'customer_id' => $c1->id,
            'item_name' => '22K Gold Chain with Pendant',
            'category' => 'Chain',
            'gross_weight' => 12.000,
            'stone_weight' => 1.200,
            'net_gold_weight' => 10.800,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 73980.00,
            'packet_number' => 'PKT-1002',
            'locker_reference' => 'Locker A - Tray 1',
            'status' => 'Pledged',
        ]);

        // Payment for Loan 1: 2 months interest collected
        Payment::create([
            'payment_no' => 'REC-2026-0001',
            'loan_id' => $l1->id,
            'customer_id' => $c1->id,
            'payment_date' => Carbon::today()->subMonths(1)->toDateString(),
            'payment_type' => 'interest_only',
            'interest_amount' => 3600.00,
            'principal_amount' => 0.00,
            'total_amount_paid' => 3600.00,
            'payment_mode' => 'upi',
            'reference_number' => 'UPI/402918291028',
            'principal_balance_after' => 120000.00,
            'notes' => '2 months interest paid via GPay',
            'received_by' => $staff->id,
            'is_immutable' => true,
        ]);

        // LOAN 2: Active Loan (Sunita Patil)
        $l2Issue = Carbon::today()->subMonths(1)->toDateString();
        $l2Maturity = Carbon::today()->addMonths(11)->toDateString();
        $l2 = Loan::create([
            'loan_no' => 'LN-2026-0002',
            'customer_id' => $c2->id,
            'principal_amount' => 45000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => $l2Issue,
            'maturity_date' => $l2Maturity,
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 10.200,
            'total_stone_weight' => 0.000,
            'total_net_weight' => 10.200,
            'total_valuation_amount' => 69870.00,
            'ltv_ratio' => 64.41,
            'total_interest_paid' => 675.00,
            'total_principal_paid' => 5000.00,
            'current_balance_principal' => 40000.00,
            'status' => 'Active',
            'notes' => 'Household gold jewellery pledge',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l2->id,
            'customer_id' => $c2->id,
            'item_name' => '22K Gold Mangalsutra',
            'category' => 'Mangalsutra',
            'gross_weight' => 10.200,
            'stone_weight' => 0.000,
            'net_gold_weight' => 10.200,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 69870.00,
            'packet_number' => 'PKT-1003',
            'locker_reference' => 'Locker A - Tray 2',
            'status' => 'Pledged',
        ]);

        Payment::create([
            'payment_no' => 'REC-2026-0002',
            'loan_id' => $l2->id,
            'customer_id' => $c2->id,
            'payment_date' => Carbon::today()->subDays(5)->toDateString(),
            'payment_type' => 'partial_both',
            'interest_amount' => 675.00,
            'principal_amount' => 5000.00,
            'total_amount_paid' => 5675.00,
            'payment_mode' => 'cash',
            'reference_number' => 'CASH-COUNTER',
            'principal_balance_after' => 40000.00,
            'notes' => 'Interest + ₹5,000 principal reduction',
            'received_by' => $staff->id,
            'is_immutable' => true,
        ]);

        // LOAN 3: Overdue Loan (Mohammed Aslam Merchant)
        $l3Issue = Carbon::today()->subMonths(14)->toDateString();
        $l3Maturity = Carbon::today()->subMonths(2)->toDateString(); // 60 days overdue!
        $l3 = Loan::create([
            'loan_no' => 'LN-2026-0003',
            'customer_id' => $c3->id,
            'principal_amount' => 35000.00,
            'interest_rate' => 1.75,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => $l3Issue,
            'maturity_date' => $l3Maturity,
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 8.400,
            'total_stone_weight' => 0.400,
            'total_net_weight' => 8.000,
            'total_valuation_amount' => 54800.00,
            'ltv_ratio' => 63.87,
            'total_interest_paid' => 3675.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 35000.00,
            'status' => 'Overdue',
            'notes' => 'Maturity passed. Telephonic reminder sent.',
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l3->id,
            'customer_id' => $c3->id,
            'item_name' => '22K Gent\'s Gold Ring with Ruby',
            'category' => 'Ring',
            'gross_weight' => 8.400,
            'stone_weight' => 0.400,
            'net_gold_weight' => 8.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 54800.00,
            'packet_number' => 'PKT-1004',
            'locker_reference' => 'Locker B - Tray 1',
            'status' => 'Pledged',
        ]);

        Payment::create([
            'payment_no' => 'REC-2026-0003',
            'loan_id' => $l3->id,
            'customer_id' => $c3->id,
            'payment_date' => Carbon::today()->subMonths(6)->toDateString(),
            'payment_type' => 'interest_only',
            'interest_amount' => 3675.00,
            'principal_amount' => 0.00,
            'total_amount_paid' => 3675.00,
            'payment_mode' => 'cash',
            'reference_number' => 'CASH-COUNTER',
            'principal_balance_after' => 35000.00,
            'notes' => '6 months interest settled in cash',
            'received_by' => $staff->id,
            'is_immutable' => true,
        ]);

        // LOAN 4: Closed / Settled Loan with Gold Released (Priya Shah)
        $l4Issue = Carbon::today()->subMonths(6)->toDateString();
        $l4Maturity = Carbon::today()->addMonths(6)->toDateString();
        $l4Closed = Carbon::today()->subDays(10)->toDateString();
        $l4 = Loan::create([
            'loan_no' => 'LN-2026-0004',
            'customer_id' => $c4->id,
            'principal_amount' => 80000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => $l4Issue,
            'maturity_date' => $l4Maturity,
            'tenure_months' => 12,
            'total_pledged_items' => 2,
            'total_gross_weight' => 15.000,
            'total_stone_weight' => 0.000,
            'total_net_weight' => 15.000,
            'total_valuation_amount' => 111750.00,
            'ltv_ratio' => 71.59,
            'total_interest_paid' => 7200.00,
            'total_principal_paid' => 80000.00,
            'current_balance_principal' => 0.00,
            'status' => 'Closed',
            'closed_at' => Carbon::today()->subDays(10),
            'settlement_discount' => 200.00,
            'notes' => 'Fully settled and gold handed over to borrower.',
            'created_by' => $admin->id,
        ]);

        $item4a = PledgedItem::create([
            'loan_id' => $l4->id,
            'customer_id' => $c4->id,
            'item_name' => '24K Gold Coin (10g)',
            'category' => 'Coin',
            'gross_weight' => 10.000,
            'stone_weight' => 0.000,
            'net_gold_weight' => 10.000,
            'purity' => '24K',
            'gold_rate_per_gram' => 7450.00,
            'valuation_amount' => 74500.00,
            'packet_number' => 'PKT-1005',
            'locker_reference' => 'Locker B - Vault Coin Box',
            'status' => 'Released',
            'released_at' => Carbon::today()->subDays(10),
            'release_receipt_no' => 'REL-2026-0001',
        ]);

        $item4b = PledgedItem::create([
            'loan_id' => $l4->id,
            'customer_id' => $c4->id,
            'item_name' => '22K Gold Earrings (Pair)',
            'category' => 'Earring',
            'gross_weight' => 5.000,
            'stone_weight' => 0.000,
            'net_gold_weight' => 5.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 34250.00,
            'packet_number' => 'PKT-1006',
            'locker_reference' => 'Locker B - Tray 2',
            'status' => 'Released',
            'released_at' => Carbon::today()->subDays(10),
            'release_receipt_no' => 'REL-2026-0001',
        ]);

        // Full settlement payment for Loan 4
        Payment::create([
            'payment_no' => 'REC-2026-0004',
            'loan_id' => $l4->id,
            'customer_id' => $c4->id,
            'payment_date' => $l4Closed,
            'payment_type' => 'full_settlement',
            'interest_amount' => 7200.00,
            'principal_amount' => 80000.00,
            'penalty_amount' => 0.00,
            'discount_amount' => 200.00,
            'total_amount_paid' => 87000.00,
            'payment_mode' => 'bank_transfer',
            'reference_number' => 'NEFT/HDFC29819283',
            'principal_balance_after' => 0.00,
            'notes' => 'Full loan closure payment received via HDFC NEFT',
            'received_by' => $admin->id,
            'is_immutable' => true,
        ]);

        // Gold Release for Loan 4
        GoldRelease::create([
            'release_no' => 'REL-2026-0001',
            'loan_id' => $l4->id,
            'customer_id' => $c4->id,
            'release_date' => $l4Closed,
            'items_count' => 2,
            'total_net_weight' => 15.000,
            'handed_over_to' => 'Priya Nilesh Shah',
            'recipient_relation' => 'self',
            'recipient_id_proof' => 'Aadhaar: 3210 9876 5432',
            'recipient_signature_note' => 'Received gold ornaments in good sealed condition',
            'is_authorized_override' => false,
            'processed_by' => $admin->id,
        ]);

        // LOAN 5: Renewed Loan (Vijay Kadam)
        $l5OriginalIssue = Carbon::today()->subMonths(13)->toDateString();
        $l5RenewalDate = Carbon::today()->subMonths(1)->toDateString();
        $l5NewMaturity = Carbon::today()->addMonths(11)->toDateString();

        $l5 = Loan::create([
            'loan_no' => 'LN-2026-0005',
            'customer_id' => $c5->id,
            'principal_amount' => 50000.00,
            'interest_rate' => 1.50,
            'interest_rate_type' => 'per_month',
            'interest_calculation_method' => 'monthly',
            'issue_date' => $l5OriginalIssue,
            'maturity_date' => $l5NewMaturity,
            'tenure_months' => 12,
            'total_pledged_items' => 1,
            'total_gross_weight' => 12.500,
            'total_stone_weight' => 0.500,
            'total_net_weight' => 12.000,
            'total_valuation_amount' => 82200.00,
            'ltv_ratio' => 60.83,
            'total_interest_paid' => 9000.00,
            'total_principal_paid' => 0.00,
            'current_balance_principal' => 50000.00,
            'status' => 'Active',
            'notes' => 'Loan renewed on ' . Carbon::parse($l5RenewalDate)->format('d/m/Y'),
            'created_by' => $admin->id,
        ]);

        PledgedItem::create([
            'loan_id' => $l5->id,
            'customer_id' => $c5->id,
            'item_name' => '22K Gold Bracelet',
            'category' => 'Bracelet',
            'gross_weight' => 12.500,
            'stone_weight' => 0.500,
            'net_gold_weight' => 12.000,
            'purity' => '22K',
            'gold_rate_per_gram' => 6850.00,
            'valuation_amount' => 82200.00,
            'packet_number' => 'PKT-1007',
            'locker_reference' => 'Locker B - Tray 3',
            'status' => 'Pledged',
        ]);

        $renewalPayment = Payment::create([
            'payment_no' => 'REC-2026-0005',
            'loan_id' => $l5->id,
            'customer_id' => $c5->id,
            'payment_date' => $l5RenewalDate,
            'payment_type' => 'interest_only',
            'interest_amount' => 9000.00,
            'principal_amount' => 0.00,
            'total_amount_paid' => 9000.00,
            'payment_mode' => 'upi',
            'reference_number' => 'UPI/RENEWAL5019',
            'principal_balance_after' => 50000.00,
            'notes' => '12 months interest cleared for renewal',
            'received_by' => $admin->id,
            'is_immutable' => true,
        ]);

        LoanRenewal::create([
            'renewal_no' => 'RNW-2026-0001',
            'loan_id' => $l5->id,
            'customer_id' => $c5->id,
            'previous_maturity_date' => Carbon::parse($l5OriginalIssue)->addMonths(12)->toDateString(),
            'new_maturity_date' => $l5NewMaturity,
            'renewal_date' => $l5RenewalDate,
            'interest_cleared_amount' => 9000.00,
            'payment_id' => $renewalPayment->id,
            'principal_adjusted' => 0.00,
            'new_principal_amount' => 50000.00,
            'renewal_fee' => 0.00,
            'notes' => 'Annual renewal executed upon 100% interest clearance.',
            'processed_by' => $admin->id,
        ]);

        // Record initial financial logs
        FinancialLog::record('system_initialized', 'Girvi ERP demo dataset and accounts initialized', 0);
    }
}
