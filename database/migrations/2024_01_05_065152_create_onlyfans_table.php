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
        Schema::create('onlyfans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->integer('cant_suscriptores')->default(0);
            $table->integer('cant_fotos')->default(0);
            $table->integer('cant_videos')->default(0);
            $table->integer('cant_posts')->default(0);
            $table->decimal('precio_membresia', 2)->default(0);
            $table->integer('show_more_social_medias')->default(1);
            $table->integer('usuarios_comunicacion')->default(1);
            $table->integer('cant_ganancias')->default(0);
            $table->string('tiempo_creacion')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users');
            // $table
            //     ->foreignId('categoria_id')
            //     ->nullable()
            //     ->constrained('categories');
            // $table
            //     ->foreignId('location_id')
            //     ->nullable()
            //     ->constrained('locations');
            $table->string('slug')->unique();
            $table->string('url')->unique();
            $table->string('imagen')->nullable();
            $table->string('banner')->nullable();
            $table->integer('views')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onlyfans');
    }
};
