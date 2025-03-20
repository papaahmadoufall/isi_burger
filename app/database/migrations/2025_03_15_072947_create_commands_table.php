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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('statut', ['en attente', 'en préparation', 'prête', 'payée'])->default('en attente');
            $table->string('total');
            $table -> foreignId('user_id')-> constrained('users')->onDelete("cascade");
            $table -> foreignId("payment_id")->constrained('payments')-> onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
