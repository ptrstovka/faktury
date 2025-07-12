<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->boolean('vat_enabled');
            $table->decimal('default_vat_rate');
            $table->integer('invoice_due_days');
            $table->string('invoice_numbering_format');
            $table->integer('next_invoice_number');
            $table->string('invoice_payment_method');
            $table->text('invoice_footer_note')->nullable();
            $table->string('invoice_template');
            $table->string('document_created_by')->nullable();
            $table->string('document_created_by_email')->nullable();
            $table->string('document_created_by_phone')->nullable();
            $table->string('document_created_by_website')->nullable();
            $table->foreignId('signature_id')->nullable()->constrained('signatures');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
