<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTransactionStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('transaction_status', function(Blueprint $table)
		{
			$table->foreign('transaction_id', 'transaction_status')->references('id')->on('transaction')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('transaction_status', function(Blueprint $table)
		{
			$table->dropForeign('transaction_status');
		});
	}

}
