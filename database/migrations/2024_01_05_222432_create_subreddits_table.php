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
        Schema::create('subreddits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tags');
            $table->boolean('verification');
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('onlyfans_subreddits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('model_id')->constrained('onlyfans');
            $table->foreignId('subreddit_id')->constrained('subreddits');
            $table->integer('verification_status')->nullable();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('model_id')->constrained('onlyfans');
            $table->foreignId('subreddit_id')->constrained('subreddits');
            $table->integer('status');
            $table->dateTimeTz('posted_at');
            $table->timestamps();
        });

        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('telegram_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tags');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('model_id')->constrained('onlyfans');
            $table->integer('status');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->string('local_media_file')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('model_id')->constrained('onlyfans');
            $table->foreignId('subreddit_id')->nullable()->constrained('subreddits');
            $table->foreignId('telegram_channel_id')->nullable()->constrained('telegram_channels');
            $table->foreignId('post_type_id')->constrained('post_types');
            $table->dateTimeTz('posted_at');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subreddits');
        Schema::dropIfExists('customer_subreddits');
        Schema::dropIfExists('events');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_types');
        Schema::dropIfExists('telegram_channels');
    }
};
