<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meta_accounts', function (Blueprint $table) {
            $table->string('account_name')->nullable()->after('meta_business_id');
            $table->string('currency')->nullable()->after('account_name');
            $table->string('timezone')->nullable()->after('currency');
            $table->json('ad_accounts')->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('meta_accounts', function (Blueprint $table) {
            $table->dropColumn(['account_name', 'currency', 'timezone', 'ad_accounts']);
        });
    }
};
