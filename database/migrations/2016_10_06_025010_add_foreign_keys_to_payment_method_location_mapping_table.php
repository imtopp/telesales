<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPaymentMethodLocationMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payment_method_location_mapping', function(Blueprint $table)
		{
			$table->foreign('location_district_id', 'payment_method_location_district_id')->references('id')->on('location_district')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('payment_method_id', 'payment_method_location_payment_method_id')->references('id')->on('payment_method')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payment_method_location_mapping', function(Blueprint $table)
		{
			$table->dropForeign('payment_method_location_district_id');
			$table->dropForeign('payment_method_location_payment_method_id');
		});
	}

}
