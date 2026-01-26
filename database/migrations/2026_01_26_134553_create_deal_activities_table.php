<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deal_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->text('description');
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index('deal_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('occurred_at');

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_activities');
    }
};
