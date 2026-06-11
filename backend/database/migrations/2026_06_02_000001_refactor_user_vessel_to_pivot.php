<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['vessel_id']);
            $table->dropColumn('vessel_id');
        });

        Schema::create('user_vessel', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vessel_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'vessel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_vessel');

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('vessel_id')->nullable()->after('active')->constrained()->nullOnDelete();
        });
    }
};
