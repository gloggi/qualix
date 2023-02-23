<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeUniqueConstraintOnInvitations extends Migration {

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('invitations', function(Blueprint $table){
		    $table->dropUnique('email');
		    $table->unique(['email', 'course_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
	    Schema::table('invitations', function(Blueprint $table) {
            $table->dropUnique(['email', 'course_id']);
            $table->unique('email', 'email');
        });
	}

}
