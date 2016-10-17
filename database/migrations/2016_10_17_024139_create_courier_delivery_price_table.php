<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourierDeliveryPriceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courier_delivery_price', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('courier_location_mapping_id')->nullable()->index('courier_delivery_price_courier_location_mapping');
			$table->bigInteger('courier_price_category_id')->nullable()->index('courier_delivery_price_courier_price_category_id');
			$table->bigInteger('price')->nullable();
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
		Schema::drop('courier_delivery_price');
	}

}
