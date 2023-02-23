<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQkTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('qk', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('quali_kategorie');
			$table->integer('kurs_id')->index('fk_kurs_qk');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('qk');
	}

}
