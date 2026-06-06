<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'DELETED'])->default('PAUSED');
            $table->enum('format', ['image', 'video', 'carousel'])->default('image');
            $table->string('headline')->nullable();
            $table->text('body')->nullable();
            $table->string('link_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('call_to_action')->default('LEARN_MORE');
            $table->string('meta_ad_id')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
