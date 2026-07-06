<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path', 500);
            $table->string('referer', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('device', 20)->nullable();      // mobile / desktop
            $table->string('user_agent', 300)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('created_at');
            $table->index('session_id');
            $table->index('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
