<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table): void {
            $table->string('url')->nullable()->after('channels');
            $table->string('label')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table): void {
            $table->dropColumn(['url', 'label']);
        });
    }
};
