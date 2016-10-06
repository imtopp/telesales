<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourierLocationMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courier_location_mapping', function(Blueprint $table)
		{
			$table->foreign('courier_package_id', 'courier_mapping_courier_package_id')->references('id')->on('courier_package')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('location_district_id', 'courier_mapping_location_district_id')->references('id')->on('location_district')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courier_location_mapping', function(Blueprint $table)
		{
			$table->dropForeign('courier_mapping_courier_package_id');
			$table->dropForeign('courier_mapping_location_district_id');
		});
	}

}
