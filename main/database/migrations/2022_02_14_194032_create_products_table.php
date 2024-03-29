<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_id');
            $table->string('title');
            $table->json('composition')
				->nullable();
            $table->decimal('price', 9, 2);
            $table->unsignedInteger('amount')
				->nullable();
			$table->unsignedInteger('thumbnail_id')
				->nullable();
			$table->unsignedTinyInteger('is_active')
				->default(1);
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
        Schema::dropIfExists('products');
    }
}
