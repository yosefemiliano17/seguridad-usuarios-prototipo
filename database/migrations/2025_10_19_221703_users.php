<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {  
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('session_status');
            $table->tinyInteger('failure_attempts')->default(0);
            $table->timestamp('lock_until')->nullable();
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
