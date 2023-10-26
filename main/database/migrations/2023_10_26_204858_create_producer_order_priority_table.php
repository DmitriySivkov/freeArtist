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
        Schema::create('producer_order_priority', function (Blueprint $table) {
			$table->id();
            $table->unsignedInteger('producer_id');
			$table->unsignedInteger('status');
			$table->json('order_priority');

			$table->index(['producer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producer_order_priority');
    }
};
