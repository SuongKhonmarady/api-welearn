<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholarship', function (Blueprint $table) {
            $table->id();
            $table->string('link');
            $table->text('description')->nullable();
            $table->date('post_at');
            $table->date('deadline');
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship');
    }
};
