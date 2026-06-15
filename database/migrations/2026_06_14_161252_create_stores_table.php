<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('followers_count')->default(0);
            $table->integer('number_of_posts')->default(1);
            $table->string('name');
            $table->string('phone_number');
            $table->string('location')->nullable();
            $table->string('city')->index()->nullable();
            $table->string('region')->index()->nullable();
            $table->string('district')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('x')->nullable();
            $table->string('country');
            $table->char('country_code', 2)->index()->default('GH');
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->decimal('longitude', 11, 8)->index();
            $table->decimal('latitude', 11, 8)->index();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->index()->default(false);
            $table->boolean('is_online')->index()->default(true);
            $table->json('policies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
