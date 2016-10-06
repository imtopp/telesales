<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationProvinceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('location_province', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->nullable();
			$table->enum('status', array('inactive','active'))->nullable()->default('active');
			$table->timestamp('input_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('input_by')->nullable();
			$table->timestamp('update_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('update_by')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('location_province');
	}

}
