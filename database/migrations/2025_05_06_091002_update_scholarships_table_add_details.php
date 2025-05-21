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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->date('post_at');
            $table->date('deadline')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('official_link')->nullable();
            $table->text('eligibility')->nullable();
            $table->string('host_country')->nullable();
            $table->string('host_university')->nullable();
            $table->string('program_duration')->nullable();
            $table->string('degree_offered')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
