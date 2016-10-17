<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourierPriceCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courier_price_category', function(Blueprint $table)
		{
			$table->foreign('courier_id', 'courier_price_category_courier_id')->references('id')->on('courier')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courier_price_category', function(Blueprint $table)
		{
			$table->dropForeign('courier_price_category_courier_id');
		});
	}

}
