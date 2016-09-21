<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDeliveryPriceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('delivery_price', function(Blueprint $table)
		{
			$table->foreign('payment_method_location_mapping_id', 'delivery_price_payment_method_location_maping_id')->references('id')->on('payment_method_location_mapping')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('total_price_category_id', 'delivery_price_total_price_category_id')->references('id')->on('total_price_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('delivery_price', function(Blueprint $table)
		{
			$table->dropForeign('delivery_price_payment_method_location_maping_id');
			$table->dropForeign('delivery_price_total_price_category_id');
		});
	}

}
