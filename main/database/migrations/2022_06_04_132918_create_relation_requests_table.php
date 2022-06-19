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
			$table->unsignedBigInteger('from_id');
			$table->string('from_type');
			$table->unsignedBigInteger('to_id');
			$table->string('to_type');
			$table->unsignedTinyInteger('status')
				->default(\App\Models\RelationRequest::STATUS_PENDING['id']);
			$table->text('message')
				->nullable();
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
        Schema::dropIfExists('relation_requests');
    }
}
