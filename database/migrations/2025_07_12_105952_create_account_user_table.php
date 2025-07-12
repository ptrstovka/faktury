<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_user', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('role');
            $table->primary(['account_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_user');
    }
};
