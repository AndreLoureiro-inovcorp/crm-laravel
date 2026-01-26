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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('public_form_id');
            $table->json('data');
            $table->string('ip_address');
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index('public_form_id');
            $table->index('submitted_at');

            $table->foreign('public_form_id')
                ->references('id')
                ->on('public_forms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
