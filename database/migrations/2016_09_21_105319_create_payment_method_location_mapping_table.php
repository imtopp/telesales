<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentMethodLocationMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_method_location_mapping', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('location_district_id')->index('payment_method_location_district_id');
			$table->integer('payment_method_id')->index('payment_method_location_payment_method_id');
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
		Schema::drop('payment_method_location_mapping');
	}

}
