<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCustomerLocationCityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customer_location_city', function(Blueprint $table)
		{
			$table->foreign('province_id', 'customer_location_city_province_id')->references('id')->on('customer_location_province')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customer_location_city', function(Blueprint $table)
		{
			$table->dropForeign('customer_location_city_province_id');
		});
	}

}
