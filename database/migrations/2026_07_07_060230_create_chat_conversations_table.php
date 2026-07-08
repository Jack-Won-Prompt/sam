<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 40)->unique();          // 게스트/식별 토큰(비공개 채널명)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();             // 고객 표시명
            $table->string('status')->default('open');      // open / closed
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_admin')->default(0);    // 관리자 미확인 수
            $table->unsignedInteger('unread_customer')->default(0); // 고객 미확인 수
            $table->timestamps();

            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
