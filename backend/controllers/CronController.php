<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\Invoice;
use common\models\EnrolmentScheduleDay;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class CronController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Lesson models.
     * @return mixed
     */
    public function actionDailyInvoice()
    {
		$today = date('w',strtotime('today'));
		$enrolmentScheduleDays = EnrolmentScheduleDay::find()->where(['day' => $today])->all();
		foreach($enrolmentScheduleDays as $enrolmentScheduleDay){
			$lesson = new Lesson();
			$lesson->enrolment_schedule_day_id = $enrolmentScheduleDay->id;
			$lesson->save();
		}
		
		$unInvoicedLessons = Lesson::find()->unInvoiced()->all();
		foreach($unInvoicedLessons as $unInvoicedLesson){
			$invoice = new Invoice();
			$invoice->lesson_id = $unInvoicedLesson->id;
			$amount = $unInvoicedLesson->enrolmentScheduleDay->enrolment->qualification->program->rate;
			$invoice->amount = $amount;
			$dateTime = new \DateTime();
			$date = $dateTime->format('Y-m-d H:i:s'); 
			$invoice->date = $date;
			$invoice->status = 1;
			$invoice->save();
		}
    }
}
