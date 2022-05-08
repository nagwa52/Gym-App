<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedDecimal('price', $precision = 8, $scale = 2);
            $table->unsignedInteger('sessions_amount');

            $table->string("has_packages_type")->nullable();
            $table->unsignedBigInteger("has_packages_id")->nullable();
            $table->index(["has_packages_type", "has_packages_id"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
