<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('reach')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->decimal('spend', 12, 2)->default(0);
            $table->decimal('cpm', 10, 4)->default(0);
            $table->decimal('cpc', 10, 4)->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('cost_per_conversion', 10, 4)->default(0);
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_insights');
    }
};
