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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->string('slug', 255);
			$table->string('nom', 255);
			$table->string('email', 255)->unique();
			$table->integer('tel')->nullable();
			$table->string('username')->nullable();
			$table->string('password', 255);
            $table->boolean('status')->default(true);
            $table->boolean('Isadmin')->default(false);
            $table->integer('solde')->default(0);
            $table->string('photo', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
