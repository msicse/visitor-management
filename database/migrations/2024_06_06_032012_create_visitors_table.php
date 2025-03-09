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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('visitor_card_id')->nullable();
            $table->string('name');
            $table->string('visitor_type');
            $table->string('organization');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('reason')->nullable();
            $table->text('address')->nullable();
            $table->text('remarks')->nullable();
            $table->text('image')->nullable();
            $table->boolean('checkout')->default(false);
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
        Schema::dropIfExists('visitors');
    }
};
