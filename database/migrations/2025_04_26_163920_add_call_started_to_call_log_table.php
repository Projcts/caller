<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->boolean('call_started')->default(false)->after('id');
            $table->string('call_terminated_by')->nullable()->after('call_started');
            $table->timestamp('start_time')->nullable()->after('call_terminated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropColumn('call_started');
            $table->dropColumn('call_terminated_by');
            $table->dropColumn('start_time');
        });
    }
};
