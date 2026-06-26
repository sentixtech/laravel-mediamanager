<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('original_name');
            $table->string('url');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('extension', 20);
            $table->string('mime_type');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('metadata')->nullable();
            $table->nullableMorphs('user');
            $table->softDeletes();
            $table->timestamps();

            $table->index('extension');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};