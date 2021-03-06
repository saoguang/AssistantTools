﻿<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAttendanceRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_attendance_record', function (Blueprint $table) {
            $table->increments('attendance_record_id');
            $table->string('course_type', 10)->nullable(false)->comment('课程类型');
            $table->integer('course_id')->unsigned()->nullable()->comment('课程ID');
            $table->integer('user_id')->unsigned()->unique()->nullable(false)->comment('用户ID');
            $table->integer('class_id')->unsigned()->nullable(false)->comment('班级ID');
            $table->string('attendance_record_status', 10)->nullable(false)->comment('课程记录状态');
            
            $table->integer('leavers_num')->unsigned()->nullable()->comment('请假人数');
            $table->string('leave_detail', 512)->nullable()->comment('请假情况');
            $table->integer('absenteeism_num')->unsigned()->nullable()->comment('旷课人数');
            $table->string('absenteeism_detail', 512)->nullable()->comment('旷课情况');
            $table->integer('mobile_num')->nullable()->comment('手机入袋数量');
            $table->string('mobile_detail_picture_file_name', 512)->nullable()->comment('手机入袋情况图片文件名');

            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->string('deleter', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_attendance_record');
    }
}
