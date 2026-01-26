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
        Schema::create('deal_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->string('file_path');
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamps();

            $table->index('deal_id');

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_proposals');
    }
};
