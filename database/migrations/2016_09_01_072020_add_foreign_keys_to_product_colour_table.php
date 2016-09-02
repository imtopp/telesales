<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductColourTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('product_colour', function(Blueprint $table)
		{
			$table->foreign('product_id', 'product_colour_product_id')->references('id')->on('product')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('product_colour', function(Blueprint $table)
		{
			$table->dropForeign('product_colour_product_id');
		});
	}

}
