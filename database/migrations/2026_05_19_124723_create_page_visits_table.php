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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);             // Поддержка IPv4 и IPv6
            $table->string('city')->nullable();   // Может быть пустым, если не определен
            $table->string('address')->nullable();
            $table->string('device', 20);         // Desktop, Mobile, Tablet
            $table->string('screen_resolution')->nullable();
            $table->string('current_url')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Фиксируем точное время захода
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
