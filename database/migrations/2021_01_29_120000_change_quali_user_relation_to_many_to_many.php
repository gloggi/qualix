<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeQualiUserRelationToManyToMany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualis_users', function(Blueprint $table)
        {
            $table->integer('quali_id');
            $table->integer('user_id');
            $table->foreign('quali_id', 'fk_qualis_users_quali_id')->references('id')->on('qualis')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_qualis_users_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->primary(['quali_id','user_id']);
        });

        DB::table('qualis_users')->insertUsing(['quali_id', 'user_id'], DB::table('qualis')->select(['id', 'user_id'])->whereNotNull('user_id'));

        Schema::table('qualis', function (Blueprint $table) {
            $table->dropForeign('fk_qualis_user_id');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualis', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->foreign('user_id', 'fk_qualis_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

        // UPDATE qualis q JOIN qualis_users qu ON q.id=qu.quali_id SET q.user_id=qu.user_id
        DB::table('qualis as q')
            ->join('qualis_users as qu', 'q.id', '=', 'qu.quali_id')
            ->update(['q.user_id' => DB::raw('qu.user_id')]);

        Schema::drop('qualis_users');
    }
}
