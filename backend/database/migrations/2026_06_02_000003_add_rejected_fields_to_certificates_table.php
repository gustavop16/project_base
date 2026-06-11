<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->text('rejected_reason')->nullable()->after('approved_at');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('rejected_reason');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['rejected_reason', 'rejected_by', 'rejected_at']);
        });
    }
};
