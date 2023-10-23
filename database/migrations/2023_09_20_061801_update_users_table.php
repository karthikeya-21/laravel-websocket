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
        Schema::table('users', function (Blueprint $table) {
            $table->string('token');
            $table->integer('connection_id')->nullable();
            $table->enum('user_status', ['Offline', 'Online']);
            $table->string('user_image')->default('avatars/empty.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('connection_id');
            $table->dropColumn('user_status');
            $table->dropColumn('user_image');

        });
    }
};
