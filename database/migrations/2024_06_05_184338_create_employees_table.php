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
            $table->foreignId('department_id')->constrained('departments');
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
