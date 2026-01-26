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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->string('title');
            $table->decimal('value', 15, 2)->default(0);
            $table->string('stage')->default('lead');
            $table->integer('probability')->default(0);
            $table->date('expected_close_date')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('entity_id');
            $table->index('person_id');
            $table->index('owner_id');
            $table->index('stage');
            
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('set null');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
