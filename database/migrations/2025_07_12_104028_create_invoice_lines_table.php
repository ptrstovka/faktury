<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedInteger('position');
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 4)->nullable();
            $table->decimal('vat_rate')->nullable();
            $table->bigInteger('unit_price_vat_exclusive')->nullable();
            $table->bigInteger('total_price_vat_inclusive')->nullable();
            $table->bigInteger('total_price_vat_exclusive')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
