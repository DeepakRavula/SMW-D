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
        $cronEnabledLocations = Location::find()->cronEnabledLocations()->all();
        $count = count($cronEnabledLocations);
        Console::startProgress(0, $count, 'Processing Location.....');
        foreach ($cronEnabledLocations as $cronEnabledLocation) {
            $activeStudents = Student::find()
            ->notDeleted()
            ->location($cronEnabledLocation->locationId)
            ->active();
        $inactiveStudents = Student::find()
            ->notDeleted()
            ->location($cronEnabledLocation)
            ->leftJoin(['active_students' => $activeStudents], 'student.id = active_students.id')
            ->andWhere(['active_students.id' => null])
            ->all();
        foreach ($inactiveStudents as $student) {
            $student->updateAttributes(['status' => Student::STATUS_INACTIVE]);
        }
        $activeStudents = Student::find()
            ->notDeleted()
            ->location($cronEnabledLocation)
            ->active()
            ->all();
        foreach ($activeStudents as $student) {
            $student->updateAttributes(['status' => Student::STATUS_ACTIVE]);
        }
        Console::output("processing: " . $cronEnabledLocation->name . 'processing', Console::FG_GREEN, Console::BOLD);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

    }
}