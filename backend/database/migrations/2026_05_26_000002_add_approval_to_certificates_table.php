<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('status');
            $table->foreignId('approved_by')->nullable()->after('pdf_path')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['pdf_path', 'approved_by', 'approved_at']);
        });
    }
};
