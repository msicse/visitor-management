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
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('department_id');
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
            
            // Explicitly define foreign keys with onDelete cascade
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');
                  
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade');
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
