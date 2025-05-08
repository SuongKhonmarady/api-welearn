<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->date('deadline')->nullable(); // Add the deadline column
        });
    }

    public function down()
    {
        Schema::table('scholarships', function (Blueprint $table) {
             // Rollback the column
        });
    }
};
