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
        Schema::create('producer_payment_providers', function (Blueprint $table) {
            $table->id();
			$table->unsignedInteger('producer_id');
			$table->unsignedInteger('payment_provider_id');
			$table->json('payment_provider_data');
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
        Schema::dropIfExists('producer_payment_providers');
    }
};
