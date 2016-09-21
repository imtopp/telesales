<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeliveryPriceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_price', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('payment_method_location_mapping_id')->index('delivery_price_payment_method_location_maping_id');
			$table->integer('total_price_category_id')->index('delivery_price_total_price_category_id');
			$table->integer('price')->nullable();
			$table->string('input_by')->nullable();
			$table->timestamp('input_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('update_by')->nullable();
			$table->timestamp('update_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('delivery_price');
	}

}
