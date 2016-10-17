<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourierPriceCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courier_price_category', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('courier_id')->nullable()->index('courier_price_category_courier_id');
			$table->string('name')->nullable();
			$table->string('min_price')->nullable();
			$table->string('max_price')->nullable();
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
		Schema::drop('courier_price_category');
	}

}
