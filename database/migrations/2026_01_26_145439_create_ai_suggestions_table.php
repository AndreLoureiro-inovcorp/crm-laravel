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
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('user_id');
            $table->index('deal_id');
            $table->index('status');
            $table->index('priority');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
