<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->binary('customer_name', 65535);
			$table->binary('customer_address', 65535);
			$table->binary('customer_identity_type', 65535);
			$table->binary('customer_identity_number', 65535);
			$table->binary('customer_email', 65535);
			$table->binary('customer_mdn', 65535);
			$table->binary('customer_location_province', 65535);
			$table->binary('customer_location_city', 65535);
			$table->binary('customer_location_district', 65535);
			$table->binary('customer_delivery_address', 65535);
			$table->string('product_category');
			$table->string('product_name');
			$table->string('product_colour');
			$table->string('product_fg_code');
			$table->string('payment_method');
			$table->string('courier');
			$table->string('courier_package');
			$table->decimal('delivery_price', 10, 0);
			$table->string('refference_number');
			$table->timestamp('input_date')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('input_by');
			$table->timestamp('update_date')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('update_by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transaction');
	}

}
