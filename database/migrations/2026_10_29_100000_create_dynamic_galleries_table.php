<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('dynamic_galleries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['front', 'side'])->default('front');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('right_image')->nullable();
            $table->string('wrong_image')->nullable();
            $table->string('right_label')->default("That's right");
            $table->string('wrong_label')->default("Not like that");
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_galleries');
    }
};
