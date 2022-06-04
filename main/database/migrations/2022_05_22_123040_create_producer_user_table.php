<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducerUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producer_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_id')
				->constrained('producers')
				->onDelete('cascade');
            $table->foreignId('user_id')
				->constrained('users')
				->onDelete('cascade');
            $table->unsignedTinyInteger('user_active')
				->default(1);
            $table->timestamps();
            $table->unique(['producer_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producer_user');
    }
}
