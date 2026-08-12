<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('broadcast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained(config('permission.table_names.roles', 'roles'))->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained(config('auth.providers.users.table', 'users'))->cascadeOnDelete();
            $table->unique(['broadcast_id', 'role_id']);
            $table->unique(['broadcast_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_recipients');
    }
};
