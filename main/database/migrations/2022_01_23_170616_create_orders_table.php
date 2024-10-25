<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
			$table->unsignedInteger('transaction_id');
            $table->unsignedInteger('user_id')
				->nullable();
            $table->unsignedInteger('producer_id');
			$table->unsignedInteger('assignee_id')
				->nullable();
            $table->json('order_products');
            $table->integer('status');
			$table->date('prepare_by');
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
        Schema::dropIfExists('orders');
    }
}
