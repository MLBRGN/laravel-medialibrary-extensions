<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $connection = config('media-library-extensions.temp_database_name');
        Schema::connection($connection)->create('aliens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $connection = config('media-library-extensions.temp_database_name');
        Schema::connection($connection)->dropIfExists('aliens');
    }
};
