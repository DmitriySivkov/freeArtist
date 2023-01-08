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
			$table->unsignedInteger('from_id');
			$table->string('from_type');
			$table->unsignedInteger('to_id');
			$table->string('to_type');
			$table->unsignedTinyInteger('status')
				->default(\App\Models\RelationRequest::STATUS_PENDING['id']);
			$table->unsignedInteger('status_changed_by_user');
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
