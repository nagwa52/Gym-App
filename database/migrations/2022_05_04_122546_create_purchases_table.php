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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedDecimal('price', $precision = 8, $scale = 2);
            $table->unsignedInteger('sessions_amount');
            $table->morphs('buyable');
            $table->morphs('sellable');
            $table->timestamps();
            $table->integer('gym_id');
            $table->boolean('is_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('purchases');
    }
};
