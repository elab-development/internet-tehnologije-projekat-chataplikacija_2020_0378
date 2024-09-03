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
        //promena kolone gender u string
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->change();
        });

        //azuriranje vrednosti
        DB::table('users')
            ->where('gender', '1')
            ->update(['gender' => 'female']);

        DB::table('users')
            ->where('gender', '0')
            ->update(['gender' => 'male']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    
        DB::table('users')
            ->where('gender', 'female')
            ->update(['gender' => 1]);

        DB::table('users')
            ->where('gender', 'male')
            ->update(['gender' => 0]);

   
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('gender')->change();
        });
    }
};
