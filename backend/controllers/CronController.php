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
		$date = new \DateTime();
		$date->add(\DateInterval::createFromDateString('yesterday'));
		
		$yesterday = date('w',strtotime('yesterday'));
		if($yesterday == 0)
			$yesterday += 7; 
		$yesterday;
		
		$enrolmentScheduleDays = EnrolmentScheduleDay::find()->where(['day' => $yesterday])->all();
		foreach($enrolmentScheduleDays as $enrolmentScheduleDay){
			$lessonModel = new Lesson();
			$lessonModel->enrolment_schedule_day_id = $enrolmentScheduleDay->id;
			$lessonModel->status = Lesson::STATUS_COMPLETED;
			$lessonModel->date = $date->format('Y-m-d H:i:s');
			$lessonModel->save();
		}
		
		$unInvoicedLessons = Lesson::find()->unInvoiced()->all();
		foreach($unInvoicedLessons as $unInvoicedLesson){
			$invoiceModel = new Invoice();
			$invoiceModel->lesson_id = $unInvoicedLesson->id;
			$amount = $unInvoicedLesson->enrolmentScheduleDay->enrolment->qualification->program->rate;
			$invoiceModel->amount = $amount;
			$dateTime = new \DateTime();
			$date = $dateTime->format('Y-m-d H:i:s'); 
			$invoiceModel->date = $date;
			$invoiceModel->status = Invoice::STATUS_PAID;
			$invoiceModel->save();
		}
    }
}
