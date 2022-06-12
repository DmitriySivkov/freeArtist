<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducerUserJoinRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producer_user_requests', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('from');
			$table->unsignedBigInteger('to');
			$table->unsignedTinyInteger('type');
			$table->unsignedTinyInteger('status')
				->default(\App\Models\ProducerUserRequest::STATUS_PENDING);
			$table->text('message')
				->nullable();
            $table->timestamps();
            $table->unique(['from', 'to', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producer_user_join_requests');
    }
}
