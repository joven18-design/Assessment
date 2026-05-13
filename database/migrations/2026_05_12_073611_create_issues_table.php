<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('category'); // bug, feature_request, support, infrastructure, security
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->text('summary')->nullable(); // AI-generated summary
            $table->text('suggested_action')->nullable(); // AI-generated next action
            $table->boolean('is_escalated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
