
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->string('country')->after('deadline'); // Add the country field after deadline
        });
    }

    public function down()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
