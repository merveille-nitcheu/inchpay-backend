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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255);
			$table->integer('montant')->nullable();
            $table->string('trans_token')->nullable();
            $table->integer('tel')->nullable();
            $table->string('status')->default('success');
            $table->string('type_trans');
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('transactions');
    }
};
