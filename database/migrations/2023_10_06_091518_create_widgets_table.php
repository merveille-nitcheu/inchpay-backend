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
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255);
            $table->string('nom', 255);
            $table->string('url_redirection', 255)->nullable();
            $table->string('status', 255);
            $table->string('lien_payement', 255);
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
