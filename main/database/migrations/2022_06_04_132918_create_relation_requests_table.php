<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_requests', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('from');
			$table->unsignedBigInteger('to');
			$table->unsignedTinyInteger('type');
			$table->unsignedTinyInteger('status')
				->default(\App\Models\RelationRequest::STATUS_PENDING);
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
