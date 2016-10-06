<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLocationDistrictTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('location_district', function(Blueprint $table)
		{
			$table->foreign('city_id', 'customer_location_district')->references('id')->on('location_city')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('location_district', function(Blueprint $table)
		{
			$table->dropForeign('customer_location_district');
		});
	}

}
