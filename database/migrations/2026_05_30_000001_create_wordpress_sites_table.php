<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wordpress_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url');
            $table->string('username');
            $table->text('app_password'); // stored encrypted via model cast
            $table->string('connector_version')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->json('field_map')->nullable(); // logical name => ACF field key map
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wordpress_sites');
    }
};
