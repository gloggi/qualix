<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeleteObservationAssignmentsAndParticipantGroupsFromArchivedCourses extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::transaction(function () {
            DB::table('observation_assignments')
                ->whereIn('course_id', function ($query) {
                    $query->select('id')->from('courses')->where('archived', true);
                })
                ->delete();

            DB::table('participant_groups')
                ->whereIn('course_id', function ($query) {
                    $query->select('id')->from('courses')->where('archived', true);
                })
                ->delete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Not possible to restore deleted data
    }
}
