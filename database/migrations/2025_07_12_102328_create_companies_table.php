<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->string('business_id')->nullable();
            $table->string('vat_id')->nullable();
            $table->string('en_vat_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->text('additional_info')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('bank_address')->nullable();
            $table->text('bank_bic')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_iban')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
