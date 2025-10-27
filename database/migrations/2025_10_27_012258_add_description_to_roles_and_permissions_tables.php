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
        // Agregar columnas útiles a roles
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        // Agregar columnas útiles a permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('module')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'module']);
        });
    }
};
