<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void    
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('national_id')->nullable();
            $table->string('avatar_url')->nullable();
        
            $table->string("manageable_type")->nullable();
            $table->unsignedBigInteger("manageable_id")->nullable();
            $table->index(["manageable_type", "manageable_id"]);

            $table->rememberToken();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
};
