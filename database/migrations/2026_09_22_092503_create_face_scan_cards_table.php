<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('face_scan_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('face_scan_id')->constrained()->cascadeOnDelete();
            $table->decimal('harmony', 4, 2);
            $table->decimal('features', 4, 2);
            $table->decimal('originality', 4, 2);
            $table->decimal('description', 4, 2);
            $table->string('image_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_scan_cards');
    }
};
