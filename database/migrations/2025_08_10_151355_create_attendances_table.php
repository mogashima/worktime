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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->string('start_time')->default('');
            $table->string('end_time')->default('');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('work_value')->default(0);
            $table->timestamps();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'date']); // 同じ日付は1件だけ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
