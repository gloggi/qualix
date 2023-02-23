<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTnTable extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('tn', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('pfadiname');
			$table->string('abteilung');
			$table->integer('kurs_id')->index('fk_kurs_tn');
			$table->string('bild_url');
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::drop('tn');
	}

}
