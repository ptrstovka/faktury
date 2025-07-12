<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->boolean('draft');
            $table->boolean('sent');
            $table->boolean('paid');
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('supplier_company_id')->constrained('companies');
            $table->foreignId('customer_company_id')->constrained('companies');
            $table->string('public_invoice_number')->nullable();
            $table->integer('invoice_number')->nullable();
            $table->string('payment_method');
            $table->string('payment_identifier')->nullable();
            $table->string('currency');
            $table->unsignedBigInteger('total_vat_inclusive')->nullable();
            $table->unsignedBigInteger('total_vat_exclusive')->nullable();
            $table->foreignId('signature_id')->nullable()->constrained('signatures');
            $table->string('issued_by')->nullable();
            $table->string('issued_by_email')->nullable();
            $table->string('issued_by_phone_number')->nullable();
            $table->string('issued_by_website')->nullable();
            $table->boolean('vat_enabled');
            $table->string('locale');
            $table->string('template');
            $table->text('footer_note')->nullable();
            $table->boolean('vat_reverse_charge');
            $table->boolean('show_pay_by_square');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
