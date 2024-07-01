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
        Schema::create('visitor_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors');
            $table->string('name');
            $table->string('organization');
            $table->string('phone');
            $table->string('email');
            $table->string('visitor_card_id')->nullable();
            $table->text('address');
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_checkin')->default(false);
            $table->boolean('is_checkout')->default(false);
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_guests');
    }
};
