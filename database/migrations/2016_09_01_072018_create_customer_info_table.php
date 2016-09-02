<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_info', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->binary('name', 65535)->nullable();
			$table->binary('address', 65535)->nullable();
			$table->binary('identity_type', 65535)->nullable();
			$table->binary('identity_number', 65535)->nullable();
			$table->binary('email', 65535)->nullable();
			$table->binary('mdn', 65535)->nullable();
			$table->binary('delivery_address', 65535)->nullable();
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
		Schema::drop('customer_info');
	}

}
