<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('meta_user_id');
            $table->string('meta_ad_account_id')->nullable();
            $table->string('meta_business_id')->nullable();
            $table->text('access_token');
            $table->text('long_lived_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->json('permissions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_accounts');
    }
};
