<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;

class ProformaInvoiceController extends Controller
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->updateAttributes(['isDeleted' => true]);
        }
        
        $currentDate = new \DateTime();
        $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->location(['14','15'])
            ->privateProgram()
            ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
            ->isRegular()
            ->joinWith(['course' => function ($query) use ($priorDate) {
                $query->andWhere(['>=', 'DATE(course.endDate)', $priorDate])
                        ->confirmed();
            }])
            ->notPaymentPrefered()
            ->all();
        foreach ($enrolments as $enrolment) {
            $dateRange = $enrolment->getPaymentCycleDateRange(null, $priorDate);
            list($from_date, $to_date) = explode(' - ', $dateRange);
            $fromDate = new \DateTime($from_date);
            $toDate = new \DateTime($to_date);
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->privateLessons()
                ->between($fromDate, $toDate)
                ->enrolment($enrolment->id)
                ->invoiced();
            $query = Lesson::find()   
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->privateLessons()
                ->between($fromDate, $toDate)
                ->enrolment($enrolment->id)
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->orderBy(['lesson.date' => SORT_ASC]);
            $lessons = $query->all();
            $lessonIds = [];
            foreach ($lessons as $lesson) {
                if ($lesson->isOwing($enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            if ($lessonIds) {
                $query = Lesson::find()
                    ->andWhere(['id' => $lessonIds])
                    ->orderBy(['lesson.date' => SORT_ASC]);
                $firstLesson = $query->one();
                if (!$firstLesson->hasAutomatedPaymentRequest()) {
                    $lessons = $query->all();
                    $model = new ProformaInvoice();
                    $model->userId = $enrolment->customer->id;
                    $model->locationId = $enrolment->customer->userLocation->location_id;
                    $model->proforma_invoice_number = $model->getProformaInvoiceNumber();
                    $model->save();
                    foreach ($lessons as $lesson) {
                        $proformaLineItem = new ProformaLineItem();
                        $proformaLineItem->proformaInvoiceId = $model->id;
                        $proformaLineItem->lessonId = $lesson->id;
                        $proformaLineItem->save();
                    }
                    $model->save();
                }
            }
        }
    }

    public function actionSave()
    {
        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->save();
        }
    }

    public function actionDelete()
    {
        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->updateAttributes(['isDeleted' => true]);
        }
        return true;
    }

    public function actionTruncate()
    {
        Yii::$app->db->createCommand()->truncateTable('proforma_invoice')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_item_invoice')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_item_lesson')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_line_item')->execute();
    }
}