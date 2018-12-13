<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\Course;
use common\models\InvoiceLineItem;

class GroupLessonController extends Controller
{
    public $locationId;

    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),
            $actionID == 'refactor-price' ? ['locationId'] : []
        );
    }
    
    public function actionRefactorPrice()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $courses = Course::find()
            ->notDeleted()
            ->confirmed()
            ->regular()
            ->location($this->locationId)
            ->groupProgram()
            ->all();
        foreach ($courses as $course) {
            if ($course->courseGroup) {
                $lessons = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->notCanceled()
                    ->andWhere(['courseId' => $course->id])
                    ->orderBy('date')
                    ->all();
                $courseRate = $course->courseProgramRate->programRate;
                $lessonsPerWeekCount =  $course->courseGroup->lessonsPerWeekCount;
                $count = $course->courseGroup->weeksCount * $lessonsPerWeekCount;
                $lastLessonPrice = $courseRate - (round($courseRate / $count, 2) * ($count - 1));
                foreach ($lessons as $lesson) {
                    $lesson->updateAttributes(['programRate' => round($courseRate / $count, 2)]);
                    $lessonId = $lesson->id;
                    $lessonLineItems = InvoiceLineItem::find()
                        ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                            $query->andWhere(['lessonId' => $lessonId]);
                        }])
                        ->all();
                    foreach ($lessonLineItems as $lessonLineItem) {
                        $lessonLineItem->updateAttributes(['amount' => round($courseRate / $count, 2)]);
                    }
                }
                $lastLesson = end($lessons);
                $lastLesson->updateAttributes(['programRate' => round($lastLessonPrice, 2)]);
                $lessonId = $lastLesson->id;
                $lessonLineItems = InvoiceLineItem::find()
                    ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                        $query->andWhere(['lessonId' => $lessonId]);
                    }])
                    ->all();
                foreach ($lessonLineItems as $lessonLineItem) {
                    $lessonLineItem->updateAttributes(['amount' => round($lastLessonPrice, 2)]);
                }
            }
        }
    }
}