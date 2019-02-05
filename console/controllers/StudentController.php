<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Student;

class StudentController extends Controller
{
    public $locationId;
    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function actionSetStatusActive()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $students = Student::find()
            ->notDeleted()
            ->all();
        foreach ($students as $student) {
            $student->updateAttributes(['status' => Student::STATUS_ACTIVE]);
        }
    }

    public function actionSetStatusInActive()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $students = Student::find()
            ->notDeleted()
            ->all();
        foreach ($students as $student) {
            $student->updateAttributes(['status' => Student::STATUS_INACTIVE]);
        }
    }

    public function actionSetStatus()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $activeStudents = Student::find()
            ->notDeleted()
            ->active();
        $inactiveStudents = Student::find()
            ->notDeleted()
            ->leftJoin(['active_students' => $activeStudents], 'student.id = active_students.id')
            ->andWhere(['active_students.id' => null])
            ->all();
        foreach ($inactiveStudents as $student) {
            $student->updateAttributes(['status' => Student::STATUS_INACTIVE]);
        }
        $activeStudents = Student::find()
            ->notDeleted()
            ->active()
            ->all();
        foreach ($activeStudents as $student) {
            $student->updateAttributes(['status' => Student::STATUS_ACTIVE]);
        }
    }
}