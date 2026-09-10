<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('contact', 100);
            $table->string('organization_name')->nullable();
            $table->string('organization_type', 50)->nullable();
            $table->string('request_type', 30);
            $table->text('requirements')->nullable();
            $table->string('status', 30)->default('pending');
            $table->text('follow_up_notes')->nullable();
            $table->string('source', 50)->default('website');
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at'], 'consultations_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_requests');
    }
};
