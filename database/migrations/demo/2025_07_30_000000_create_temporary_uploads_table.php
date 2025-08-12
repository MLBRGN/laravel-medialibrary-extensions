<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('media-library-extensions.temp_database_name');
        Schema::connection($connection)->create('mle_temporary_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('disk');
            $table->string('path');
            $table->string('name')->comment('Display name of the file');
            $table->string('file_name')->comment('Stored as name of the file');
            $table->string('collection_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->json('custom_properties')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->timestamps();

            $table->index('session_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        $connection = config('media-library-extensions.temp_database_name');
        Schema::connection($connection)->dropIfExists('mle_temporary_uploads');
    }
};
