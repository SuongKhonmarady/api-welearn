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
        Schema::table('scholarships', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('official_link')->nullable();
            $table->text('eligibility')->nullable();
            $table->string('host_country')->nullable();
            $table->string('host_university')->nullable();
            $table->string('program_duration')->nullable();
            $table->string('degree_offered')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
