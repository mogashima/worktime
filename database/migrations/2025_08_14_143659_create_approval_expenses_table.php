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
        Schema::create('approval_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status_code')->default('pending');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('status_code')
                ->references('status_code')
                ->on('approval_statuses')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('reviewer_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_expenses');
    }
};
