<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Model tables
        Schema::create('evaluation_grid_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('course_id');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
        Schema::create('evaluation_grid_row_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_grid_template_id');
            $table->integer('order');
            $table->text('criterion');
            $table->string('control_type');
            $table->json('control_config');
            $table->timestamps();

            $table->foreign('evaluation_grid_template_id', 'fk_egrt_grid_template')->references('id')->on('evaluation_grid_templates')->onDelete('CASCADE');
        });
        Schema::create('evaluation_grids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_grid_template_id');
            $table->integer('block_id');
            $table->integer('user_id');
            $table->timestamps();

            $table->foreign('evaluation_grid_template_id')->references('id')->on('evaluation_grid_templates')->onDelete('cascade');
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::create('evaluation_grid_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_grid_id');
            $table->foreignId('evaluation_grid_row_template_id');
            $table->json('value');
            $table->text('notes');
            $table->timestamps();

            $table->foreign('evaluation_grid_id')->references('id')->on('evaluation_grids')->onDelete('CASCADE');
            $table->foreign('evaluation_grid_row_template_id')->references('id')->on('evaluation_grid_row_templates')->onDelete('CASCADE');
        });

        // Join tables
        Schema::create('evaluation_grid_templates_requirements', function (Blueprint $table) {
            $table->foreignId('evaluation_grid_template_id');
            $table->integer('requirement_id');

            $table->foreign('evaluation_grid_template_id', 'fk_egtrequirements_grid_template')->references('id')->on('evaluation_grid_templates')->onDelete('CASCADE');
            $table->foreign('requirement_id')->references('id')->on('requirements')->onDelete('CASCADE');
            $table->primary(['evaluation_grid_template_id','requirement_id']);
        });
        Schema::create('evaluation_grid_templates_blocks', function (Blueprint $table) {
            $table->foreignId('evaluation_grid_template_id');
            $table->integer('block_id');

            $table->foreign('evaluation_grid_template_id', 'fk_egtb_grid_template')->references('id')->on('evaluation_grid_templates')->onDelete('CASCADE');
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('CASCADE');
            $table->primary(['evaluation_grid_template_id','block_id']);
        });
        Schema::create('evaluation_grids_participants', function (Blueprint $table) {
            $table->foreignId('evaluation_grid_id');
            $table->integer('participant_id');

            $table->foreign('evaluation_grid_id')->references('id')->on('evaluation_grids')->onDelete('CASCADE');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('CASCADE');
            $table->primary(['evaluation_grid_id','participant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('evaluation_grids_participants');
        Schema::dropIfExists('evaluation_grids_blocks');
        Schema::dropIfExists('evaluation_grids_requirements');
        Schema::dropIfExists('evaluation_grid_rows');
        Schema::dropIfExists('evaluation_grids');
        Schema::dropIfExists('evaluation_grid_row_templates');
        Schema::dropIfExists('evaluation_grid_templates');
    }
};
