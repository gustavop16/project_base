<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('call_sign', 20)->nullable();
            $table->string('port_of_registry', 100)->nullable();
            $table->decimal('gross_tonnage', 12, 2)->nullable();
            $table->date('built_at')->nullable();
            $table->string('imo_number', 20)->unique()->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
