<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBeobachtungQkTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('beobachtung_qk', function(Blueprint $table)
		{
			$table->integer('beobachtung_id');
			$table->integer('qk_id')->index('fk_qk_beobachtung');
			$table->primary(['beobachtung_id','qk_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('beobachtung_qk');
	}

}
