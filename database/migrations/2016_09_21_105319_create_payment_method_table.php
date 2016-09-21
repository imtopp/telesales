<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentMethodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_method', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->nullable();
			$table->text('redirect_url', 65535)->nullable();
			$table->enum('status', array('0','1'))->nullable()->default('1');
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
		Schema::drop('payment_method');
	}

}
