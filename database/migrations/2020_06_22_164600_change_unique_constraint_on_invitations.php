<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeUniqueConstraintOnInvitations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('invitations', function(Blueprint $table){
		    $table->dropUnique('email');
		    $table->unique(['email', 'course_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	    Schema::table('invitations', function(Blueprint $table) {
            $table->dropUnique(['email', 'course_id']);
            $table->unique('email', 'email');
        });
	}

}
