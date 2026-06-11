<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_forms', function (Blueprint $table) {
            $table->foreignId('vessel_id')->nullable()->after('active')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_forms', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Vessel::class);
            $table->dropColumn('vessel_id');
        });
    }
};
