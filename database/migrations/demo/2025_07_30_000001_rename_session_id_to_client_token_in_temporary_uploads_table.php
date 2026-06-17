<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('medialibrary-extensions.demo_database_name');
        Schema::connection($connection)->table('mle_temporary_uploads', function (Blueprint $table) {
            $table->renameColumn('session_id', 'client_token');
        });
    }

    public function down(): void
    {
        $connection = config('medialibrary-extensions.demo_database_name');
        Schema::connection($connection)->table('mle_temporary_uploads', function (Blueprint $table) {
            $table->renameColumn('client_token', 'session_id');
        });
    }
};
