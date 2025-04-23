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
        Schema::create('upload_files', function (Blueprint $table) {
    $table->id();
    $table->foreignId('upload_session_id')->constrained()->onDelete('cascade');
    $table->string('original_name');
    $table->string('stored_name');
    $table->string('mime_type');
    $table->unsignedBigInteger('size');
    $table->unsignedInteger('download_count')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                Schema::dropIfExists('upload_files');

    }
};
