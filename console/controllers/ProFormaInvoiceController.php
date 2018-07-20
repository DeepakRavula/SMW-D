<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;

class ProFormaInvoiceController extends Controller
{
    public $locationId;

    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function actionPaymentRequest()
    {
        $date = new \DateTime('2018-08-30');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->privateProgram()
            ->isRegular()
            ->all();
        foreach ($enrolments as $enrolment) {
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->andWhere(['<', 'DATE(lesson.date)', $date->format('Y-m-d')])
                ->enrolment($enrolment->id)
                ->all();
            $lessonIds = [];
            foreach ($lessons as $lesson) {
                if ($lesson->isOwing($enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            if ($lessonIds) {
                $model = new ProformaInvoice();
                $model->userId = $enrolment->customer->id;
                $model->locationId = $enrolment->customer->userLocation->location_id;
                $model->proforma_invoice_number = $model->getProformaInvoiceNumber();
                $model->save();
                $lessons = Lesson::findAll($lessonIds);
                foreach ($lessons as $lesson) {
                    $proformaLineItem = new ProformaLineItem();
                    $proformaLineItem->proformaInvoiceId = $model->id;
                    $proformaLineItem->lessonId = $lesson->id;
                    $proformaLineItem->save();
                }
            }
        }
    }
}