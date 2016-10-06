<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourierGedDeliveryPriceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courier_ged_delivery_price', function(Blueprint $table)
		{
			$table->foreign('courier_ged_price_category_id', 'courier_ged_delivery_price_courier_ged_price_category')->references('id')->on('courier_ged_price_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('courier_location_mapping_id', 'courier_ged_delivery_price_courier_location_mapping_id')->references('id')->on('courier_location_mapping')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courier_ged_delivery_price', function(Blueprint $table)
		{
			$table->dropForeign('courier_ged_delivery_price_courier_ged_price_category');
			$table->dropForeign('courier_ged_delivery_price_courier_location_mapping_id');
		});
	}

}
