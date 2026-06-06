<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'DELETED'])->default('PAUSED');
            $table->decimal('daily_budget', 12, 2)->nullable();
            $table->decimal('lifetime_budget', 12, 2)->nullable();
            $table->json('targeting')->nullable();
            $table->string('billing_event')->default('IMPRESSIONS');
            $table->string('optimization_goal')->default('REACH');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('meta_ad_set_id')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_sets');
    }
};
