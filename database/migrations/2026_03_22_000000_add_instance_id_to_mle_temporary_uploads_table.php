<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mle_temporary_uploads', function (Blueprint $table) {
            $table->string('instance_id')->nullable()->after('session_id');
            $table->index('instance_id');
        });
    }

    public function down(): void
    {
        Schema::table('mle_temporary_uploads', function (Blueprint $table) {
            $table->dropIndex(['instance_id']);
            $table->dropColumn('instance_id');
        });
    }
};
