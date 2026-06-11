<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_form_responses', function (Blueprint $table) {
            $table->unsignedBigInteger('certificate_id')->nullable()->after('certificate_form_id');
            $table->foreign('certificate_id', 'cfr_certificate_fk')
                  ->references('id')->on('certificates')->nullOnDelete();

            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_form_responses', function (Blueprint $table) {
            $table->dropForeign('cfr_certificate_fk');
            $table->dropColumn('certificate_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
