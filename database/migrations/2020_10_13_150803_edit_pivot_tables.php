<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPivotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->boolean('is_leader')->default(false);
        });
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('is_leader');
        });
        Schema::table('role_user', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable();
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
