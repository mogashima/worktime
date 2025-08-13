<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_attendance_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_attendance_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_time')->nullable(false);
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_attendance_breaks');
    }
};
