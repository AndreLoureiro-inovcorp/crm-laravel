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
        Schema::create('deal_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->string('type');
            $table->string('subject');
            $table->text('body');
            $table->timestamp('sent_at');
            $table->unsignedBigInteger('sent_by');
            $table->timestamps();

            $table->index('deal_id');
            $table->index('type');

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->foreign('sent_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_emails');
    }
};
