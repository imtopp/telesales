<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCustomerInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customer_info', function(Blueprint $table)
		{
			$table->foreign('location_district_id', 'customer_info_location_district_id')->references('id')->on('location_district')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customer_info', function(Blueprint $table)
		{
			$table->dropForeign('customer_info_location_district_id');
		});
	}

}
