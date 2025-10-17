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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('hire_date');
            $table->decimal('salary', 12, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['first_name', 'last_name']);
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
