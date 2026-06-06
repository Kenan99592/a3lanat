<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meta_account_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('objective', [
                'AWARENESS',
                'TRAFFIC',
                'ENGAGEMENT',
                'APP_PROMOTION',
                'LEADS',
                'SALES'
            ]);
            $table->enum('status', ['ACTIVE', 'PAUSED', 'DELETED', 'ARCHIVED'])->default('PAUSED');
            $table->enum('budget_type', ['daily', 'lifetime'])->default('daily');
            $table->decimal('budget', 12, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('meta_campaign_id')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
