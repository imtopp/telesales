<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductFgCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_fg_code', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('fg_code')->nullable();
			$table->integer('product_colour_id')->nullable()->index('product_fg_code_product_colour_id');
			$table->float('price', 255)->nullable();
			$table->text('description', 65535)->nullable();
			$table->text('image_url', 65535)->nullable();
			$table->timestamp('input_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('input_by')->nullable();
			$table->timestamp('update_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('update_by')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_fg_code');
	}

}
