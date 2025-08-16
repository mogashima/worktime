<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('approval_attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('start_time')->default('');
            $table->string('end_time')->default('');
            $table->string('status_code')->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('attendance_id')->constrained();
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('status_code')
                ->references('status_code')
                ->on('approval_statuses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_attendances');
    }
};
