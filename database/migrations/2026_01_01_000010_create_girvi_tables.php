<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Business Settings
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique()->index();
            $table->string('name');
            $table->string('mobile')->index();
            $table->string('secondary_phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('aadhaar_number')->nullable();
            $table->string('id_proof_type')->nullable(); // Aadhaar, Voter ID, Driving License, PAN
            $table->string('id_proof_number')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        // Loans
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_no')->unique()->index();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2); // e.g. 1.50 % per month or 18% annual
            $table->string('interest_rate_type')->default('per_month'); // per_month, per_annum
            $table->string('interest_calculation_method')->default('monthly'); // monthly, daily, annual, flat, reducing_balance
            $table->date('issue_date');
            $table->date('maturity_date')->index();
            $table->integer('tenure_months')->default(12);
            $table->integer('total_pledged_items')->default(0);
            $table->decimal('total_gross_weight', 8, 3)->default(0);
            $table->decimal('total_stone_weight', 8, 3)->default(0);
            $table->decimal('total_net_weight', 8, 3)->default(0);
            $table->decimal('total_valuation_amount', 12, 2)->default(0);
            $table->decimal('ltv_ratio', 5, 2)->default(0);
            $table->decimal('total_interest_paid', 12, 2)->default(0);
            $table->decimal('total_principal_paid', 12, 2)->default(0);
            $table->decimal('current_balance_principal', 12, 2);
            $table->string('status')->default('Active')->index(); // Active, Closed, Overdue
            $table->timestamp('closed_at')->nullable();
            $table->decimal('settlement_discount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pledged Items
        Schema::create('pledged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category')->default('Ring'); // Ring, Chain, Bangle, Necklace, Coin, Earring, Bracelet, Mangalsutra, Pendant, Other
            $table->decimal('gross_weight', 8, 3);
            $table->decimal('stone_weight', 8, 3)->default(0);
            $table->decimal('net_gold_weight', 8, 3);
            $table->string('purity')->default('22K'); // 18K, 20K, 22K, 24K
            $table->decimal('gold_rate_per_gram', 10, 2);
            $table->decimal('valuation_amount', 12, 2);
            $table->string('packet_number')->nullable()->index();
            $table->string('locker_reference')->nullable()->index();
            $table->string('photo_path')->nullable();
            $table->string('status')->default('Pledged')->index(); // Pledged, Released, Auctioned
            $table->timestamp('released_at')->nullable();
            $table->string('release_receipt_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no')->unique()->index();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('payment_date')->index();
            $table->string('payment_type')->default('interest_only'); // interest_only, principal_only, partial_both, full_settlement
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('principal_amount', 12, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount_paid', 12, 2);
            $table->string('payment_mode')->default('cash'); // cash, upi, bank_transfer, cheque
            $table->string('reference_number')->nullable();
            $table->decimal('principal_balance_after', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_immutable')->default(true);
            $table->timestamps();
        });

        // Loan Renewals
        Schema::create('loan_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('renewal_no')->unique()->index();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('previous_maturity_date');
            $table->date('new_maturity_date');
            $table->date('renewal_date');
            $table->decimal('interest_cleared_amount', 12, 2)->default(0);
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('principal_adjusted', 12, 2)->default(0);
            $table->decimal('new_principal_amount', 12, 2);
            $table->decimal('renewal_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Gold Releases
        Schema::create('gold_releases', function (Blueprint $table) {
            $table->id();
            $table->string('release_no')->unique()->index();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('release_date');
            $table->integer('items_count')->default(1);
            $table->decimal('total_net_weight', 8, 3)->default(0);
            $table->string('handed_over_to');
            $table->string('recipient_relation')->default('self'); // self, spouse, nominee, authorized_rep
            $table->string('recipient_id_proof')->nullable();
            $table->text('recipient_signature_note')->nullable();
            $table->boolean('is_authorized_override')->default(false);
            $table->text('override_reason')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Financial & Audit Logs
        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index(); // loan_created, payment_received, loan_renewed, gold_released, loan_closed
            $table->string('reference_no')->nullable()->index();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('gold_releases');
        Schema::dropIfExists('loan_renewals');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('pledged_items');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('business_settings');
    }
};
