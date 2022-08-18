<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFeedbackRequirementStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirement_statuses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('color')->nullable(false);
            $table->string('icon')->nullable(false);
            $table->timestamps();
        });
        Schema::table('feedback_requirements', function(Blueprint $table) {
            $table->integer('requirement_status_id')->nullable(true);
        });
        DB::table('courses')->orderBy('id')->chunk(100, function ($courses) {
           foreach ($courses as $course) {
               $passed = DB::table('requirement_statuses')->insertGetId(['course_id' => $course->id, 'name' => 'erfüllt', 'color' => '#38c172', 'icon' => 'check-circle']);
               $pending = DB::table('requirement_statuses')->insertGetId(['course_id' => $course->id, 'name' => 'unter Beobachtung', 'color' => '#3490dc', 'icon' => 'binoculars']);
               $notPassed = DB::table('requirement_statuses')->insertGetId(['course_id' => $course->id, 'name' => 'nicht erfüllt', 'color' => '#e3342f', 'icon' => 'times-circle']);
               DB::table('feedback_requirements')
                   ->join('requirements', 'feedback_requirements.requirement_id', '=', 'requirements.id')
                   ->where(['requirements.course_id' => $course->id, 'passed' => 1])
                   ->update(['requirement_status_id' => $passed]);
               DB::table('feedback_requirements')
                   ->join('requirements', 'feedback_requirements.requirement_id', '=', 'requirements.id')
                   ->where(['requirements.course_id' => $course->id, 'passed' => 0])
                   ->update(['requirement_status_id' => $notPassed]);
               DB::table('feedback_requirements')
                   ->join('requirements', 'feedback_requirements.requirement_id', '=', 'requirements.id')
                   ->where(['requirements.course_id' => $course->id, 'passed' => null])
                   ->update(['requirement_status_id' => $pending]);
           }
        });
        Schema::table('feedback_requirements', function(Blueprint $table) {
            $table->integer('requirement_status_id')->nullable(false)->change();
            $table->foreign('requirement_status_id')->references('id')->on('requirement_statuses')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->dropColumn('passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedback_requirements', function(Blueprint $table) {
            $table->boolean('passed')->nullable();
        });
        // Attempt to restore the passed column values where the status names haven't been changed by the users
        DB::table('courses')->orderBy('id')->chunk(100, function ($courses) {
            foreach ($courses as $course) {
                $passed = DB::table('requirement_statuses')->where(['course_id' => $course->id, 'name' => 'erfüllt'])->pluck('id');
                if (count($passed) > 0) {
                    DB::table('feedback_requirements')
                        ->join('requirements', 'feedback_requirements.requirement_id', '=', 'requirements.id')
                        ->where(['requirements.course_id' => $course->id, 'requirement_status_id' => $passed])
                        ->update(['passed' => true]);
                }
                $notPassed = DB::table('requirement_statuses')->where(['course_id' => $course->id, 'name' => 'nicht erfüllt'])->pluck('id');
                if (count($notPassed) > 0) {
                    DB::table('feedback_requirements')
                        ->join('requirements', 'feedback_requirements.requirement_id', '=', 'requirements.id')
                        ->where(['requirements.course_id' => $course->id, 'requirement_status_id' => $notPassed])
                        ->update(['passed' => false]);
                }
            }
        });
        Schema::table('feedback_requirements', function(Blueprint $table) {
            $table->dropForeign('qualix.feedback_requirements.feedback_requirements_requirement_status_id_foreign');
            $table->dropColumn('requirement_status_id');
        });
        Schema::dropIfExists('requirement_statuses');
    }
}
