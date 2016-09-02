<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCustomerLocationDistrictTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customer_location_district', function(Blueprint $table)
		{
			$table->foreign('city_id', 'customer_location_district')->references('id')->on('customer_location_city')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customer_location_district', function(Blueprint $table)
		{
			$table->dropForeign('customer_location_district');
		});
	}

}
