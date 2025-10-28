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
        // Tabla para preferencias de notificaciones por usuario
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_type'); // employee_created, document_expired, etc
            $table->boolean('channel_email')->default(true);
            $table->boolean('channel_database')->default(true);
            $table->boolean('channel_sms')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Índices
            $table->unique(['user_id', 'notification_type']);
            $table->index(['user_id', 'notification_type']);
        });

        // Tabla para plantillas de notificaciones
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type')->unique(); // employee_created, etc
            $table->string('subject_email')->nullable();
            $table->text('body_email')->nullable(); // HTML con variables {{variable}}
            $table->string('body_sms', 160)->nullable(); // Máximo 160 caracteres
            $table->text('body_whatsapp')->nullable();
            $table->enum('priority', ['critical', 'important', 'informative', 'optional'])->default('informative');
            $table->json('default_channels')->nullable(); // ['email', 'database', 'sms']
            $table->timestamps();
        });

        // Tabla para logs de notificaciones (auditoría)
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id')->nullable(); // FK a notifications (puede ser null si falla antes)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_type');
            $table->enum('channel', ['email', 'database', 'sms', 'whatsapp']);
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable(); // IP, user agent, etc
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'notification_type']);
            $table->index(['status', 'sent_at']);
            $table->index('notification_id');
        });

        // Tabla para configuración de resúmenes
        Schema::create('notification_digest_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('digest_frequency', ['immediate', 'hourly', 'daily', 'weekly'])->default('daily');
            $table->time('digest_time')->default('09:00:00');
            $table->boolean('include_optional')->default(false);
            $table->timestamp('last_digest_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_digest_settings');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_preferences');
    }
};
