<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('postcode', 10)->nullable()->after('phone');
            $table->string('address1')->nullable()->after('postcode');
            $table->string('address2')->nullable()->after('address1');
            $table->boolean('is_admin')->default(false)->after('address2');
            $table->unsignedInteger('points')->default(0)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'postcode', 'address1', 'address2', 'is_admin', 'points']);
        });
    }
};
