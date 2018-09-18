<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;

class PaymentRequestController extends Controller
{
    public $locationId;

    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function actionCreate()
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
            ->location([14, 15, 16])
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
            $dateRange = $enrolment->getCurrentPaymentCycleDateRange($priorDate);
            $enrolment->createPaymentRequest($dateRange);
        }
        return true;
    }

    public function actionSave()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->save();
        }
    }

    public function actionDelete()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->updateAttributes(['isDeleted' => true]);
        }
        return true;
    }

    public function actionTruncate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        Yii::$app->db->createCommand()->truncateTable('proforma_invoice')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_item_invoice')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_item_lesson')->execute();
        Yii::$app->db->createCommand()->truncateTable('proforma_line_item')->execute();
    }
}