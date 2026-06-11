<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_forms', function (Blueprint $table) {
            $table->dropForeign(['vessel_id']);
            $table->dropColumn('vessel_id');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_forms', function (Blueprint $table) {
            $table->foreignId('vessel_id')->nullable()->after('active')->constrained()->nullOnDelete();
        });
    }
};
