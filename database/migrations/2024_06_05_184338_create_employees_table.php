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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('emply_id');
            $table->string('name');
            $table->string('designation');
            $table->string('phone');
            $table->string('email');
            $table->string('gender')->nullable();
            $table->string('blood')->nullable();
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->string('date_of_join')->nullable();
            $table->string('resign_date')->nullable();
            $table->tinyInteger('status');
            $table->text('about')->nullable();
            $table->timestamps();

            // Explicitly define foreign key with onDelete cascade
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
        Schema::dropIfExists('employees');
    }
};
