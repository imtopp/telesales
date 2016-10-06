<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('transaction', function(Blueprint $table)
		{
			$table->foreign('customer_info_id', 'transaction_customer_info_id')->references('id')->on('customer_info')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('payment_method_id', 'transaction_payment_type_id')->references('id')->on('payment_method')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('product_fg_code_id', 'transaction_product_fg_code_id')->references('id')->on('product_fg_code')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('total_price_category_id', 'transaction_total_price_category_id')->references('id')->on('courier_ged_price_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('transaction', function(Blueprint $table)
		{
			$table->dropForeign('transaction_customer_info_id');
			$table->dropForeign('transaction_payment_type_id');
			$table->dropForeign('transaction_product_fg_code_id');
			$table->dropForeign('transaction_total_price_category_id');
		});
	}

}
