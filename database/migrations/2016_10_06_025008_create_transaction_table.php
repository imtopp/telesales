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
			$table->string('prefix_id')->default('DOTS');
			$table->integer('id', true);
			$table->integer('customer_info_id')->nullable()->index('transaction_customer_info_id');
			$table->integer('product_fg_code_id')->nullable()->index('transaction_product_fg_code_id');
			$table->integer('qty')->nullable();
			$table->integer('total_price_category_id')->nullable()->index('transaction_total_price_category_id');
			$table->integer('payment_method_id')->nullable()->index('transaction_payment_type_id');
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
		Schema::drop('transaction');
	}

}
