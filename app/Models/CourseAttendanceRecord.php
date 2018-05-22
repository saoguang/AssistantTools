<?php

namespace App\Models;
//By Sao Guang
use App\Http\Controllers\DataFetch\CourseTableParser;
use App\Http\Controllers\DataFetch\DataFetchController;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * 考勤数据课程表 模型
 * Class Course
 * @package App\Models
 */
class CourseAttendanceRecord extends Model
{
    protected $table = 't_course_attendance_record';

    protected $primaryKey = 'course_id';

    /**
     * 清除原考勤课程表数据
     * @param $user_id
     * @param null $year
     * @param null $term
     */
    private static function cleanPersonalCourseTableDB($user_id, $year = null, $term = null){
        $rule = [
            ['user_id', '=', $user_id]
        ];
        if($year !== null)
            array_push($rule, ['school_year', '=', $year]);
        if($term !== null)
            array_push($rule, ['school_term', '=', $term]);
        CourseAttendanceRecord::where($rule)
            ->delete();
    }

    /**
     * 考勤课程表数据是否存在
     * @param $user_id
     * @return bool|$courses 如果课程表存在就返回课程表数据，否则返回false
     */
    public static function isPersonalCourseTableDataExist($user_id, $year, $term){
        $rule = [
            ['user_id', '=', $user_id],
            ['school_year', '=', $year],
            ['school_term', '=', $term]
        ];
        $courses = CourseAttendanceRecord::where($rule)
            ->get();
        if($courses->isEmpty())
            return false;
        else
            return $courses;
    }

    /**
     * getCourseTableData (Attendance Record)
     * @param $user_id
     * @param $year
     * @param $term
     * @param $weekth
     * @param $week
     * @return bool | ORM object
     */
    public static function getCourseTableData($user_id, $year, $term, $weekth, $week){
        $courseData = CourseAttendanceRecord::where([
            ['user_id', '=', $user_id],
            ['school_year', '=', $year],
            ['school_term', '=', $term],
            ['weekth', '=', $weekth],
            ['week', '=', $week],
        ])->get();
        if($courseData->isEmpty()){
            return false;
        }else{
            return $courseData;
        }
    }

    /**
     * 复制并解析个人课程表数据（如个人课程表数据不存在，将自动从学院服务器更新个人课程表）
     * @param $user_id
     * @param $year
     * @param $term
     * @return bool|string
     */
    public static function copyAndParsePersonalCourse($user_id, $year, $term){
        $personalCoursesData = Course::where([
            ['user_id', '=', $user_id],
            ['school_year', '=', $year],
            ['school_term', '=', $term]
        ])->get();
        if($personalCoursesData->isEmpty()){
            //个人课程表数据不存在，则更新个人课程表数据(从学院网站爬取)
            $retur = Course::updatePersonalCourseTableData($user_id, $year, $term);
            if($retur !== true){
                return '个人课表数据更新失败';
            }
        }else{
            //个人课程表数据存在，直接进行拷贝
            $courseDataParser = new CourseTableParser();//课表数据解析对象
            foreach ($personalCoursesData as $course){
                //解析周次数据
                $weekths = $courseDataParser->parseWeekth($course->weekth);
                foreach ($weekths as $key => $value){
                    $courseAttendanceRecord = new CourseAttendanceRecord();
                    $courseAttendanceRecord->course_name = $course->course_name;
                    $courseAttendanceRecord->teacher_name = $course->teacher_name;
                    $courseAttendanceRecord->position = $course->position;
                    $courseAttendanceRecord->school_year = $course->school_year;
                    $courseAttendanceRecord->schoole_term = $course->schoole_term;
                    $courseAttendanceRecord->weekth = $key;
                    $courseAttendanceRecord->week = $course->week;
                    $courseAttendanceRecord->section = $course->section;
                    $courseAttendanceRecord->user_id = $course->user_id;
                    if(!$courseAttendanceRecord->save())
                        return '考勤课程表数据创建异常!';
                }
            }
            return true;
        }
    }
}