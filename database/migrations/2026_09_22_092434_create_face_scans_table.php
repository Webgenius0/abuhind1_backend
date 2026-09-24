<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('face_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('front_image_path');
            $table->string('side_image_path')->nullable();
            $table->decimal('harmony_score', 4, 2)->nullable();
            $table->decimal('symmetry', 4, 2)->nullable();
            $table->decimal('jawline', 4, 2)->nullable();
            $table->decimal('cheekbones', 4, 2)->nullable();
            $table->decimal('nose', 4, 2)->nullable();
            $table->decimal('skin_quality', 4, 2)->nullable();
            $table->decimal('facial_width_height_ratio', 4, 2)->nullable();
            $table->json('key_strengths')->nullable();
            $table->json('regions')->nullable();
            $table->text('summary')->nullable();
            $table->string('status')->default('pending'); // pending|processed|failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_scans');
    }
};
