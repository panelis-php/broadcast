<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained(config('auth.providers.users.table', 'users'))->cascadeOnDelete();
            $table->string('channel'); // database | mail
            $table->string('status')->default('subscribed'); // subscribed | unsubscribed
            $table->timestamps();

            $table->unique(['user_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_user');
    }
};
