<?php

use yii\db\Migration;
use common\models\Course;
use common\models\CourseSchedule;

class m170623_040453_course extends Migration
{
    public function up()
    {
		foreach(Course::find()->all() as $course) {
			$courseSchedule = CourseSchedule::find()
				->andWhere(['courseId' => $course->id])
				->all();
			if(empty($courseSchedule)) {
				$courseScheduleModel = new CourseSchedule();
				$courseScheduleModel->setScenario(CourseSchedule::SCENARIO_ONE_OFF_MIGRATION);
				$courseScheduleModel->courseId = $course->id;
				$courseScheduleModel->day = $course->day;
				$courseScheduleModel->fromTime = $course->fromTime;
				$courseScheduleModel->duration = $course->duration;
				if(! $courseScheduleModel->save()) {
					Yii::error('One-off migration for course: ' . \yii\helpers\VarDumper::dumpAsString($courseScheduleModel->getErrors()));	
				}
			}
		}
    }

    public function down()
    {
        echo "m170623_040453_course cannot be reverted.\n";

        return false;
    }
}
