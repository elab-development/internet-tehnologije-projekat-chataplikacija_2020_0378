<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrationsw.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->longText('message')->nullable();//stavljamo nullable jer ponekad zelimo da posaljemo atachment bez poruke
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->nullable()->constrained('users');//stavljamo da je nullable jer poruka ne mora stici samo jednom korisniku
            $table->foreignId('group_id')->nullable()->constrained('groups');// mora postojti ili group_id ili receiver_id da bi poruka bila validna
            $table->foreignId('conversation_id')->nullable()->constrained('conversations');
            $table->timestamps();
        });

        Schema::table('groups', function (Blueprint $table){
            $table->foreignId('last_message_id')->nullable()->constrained('messages');//u tabeli grupa dodajemo kolonu last message id
        });

        Schema::table('conversations', function (Blueprint $table){
            $table->foreignId('last_message_id')->nullable()->constrained('messages');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
