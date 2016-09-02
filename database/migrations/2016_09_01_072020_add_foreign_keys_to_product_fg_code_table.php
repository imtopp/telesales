<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductFgCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('product_fg_code', function(Blueprint $table)
		{
			$table->foreign('product_colour_id', 'product_fg_code_product_colour_id')->references('id')->on('product_colour')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('product_fg_code', function(Blueprint $table)
		{
			$table->dropForeign('product_fg_code_product_colour_id');
		});
	}

}
