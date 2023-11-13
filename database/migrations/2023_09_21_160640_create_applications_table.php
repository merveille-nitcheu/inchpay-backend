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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
			$table->string('slug', 255);
			$table->string('nom', 255);
            $table->text('description')->nullable();
            $table->string('produit')->nullable();
            $table->string('url', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('categories', 255);
            $table->boolean('status')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');	
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
