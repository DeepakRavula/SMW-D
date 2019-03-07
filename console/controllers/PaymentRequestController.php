<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;
use common\models\Location;
use yii\helpers\Console;

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

    public function options($actionID)
    {
        return array_merge(parent::options($actionID),
            $actionID == 'create' ? ['locationId'] : []
        );
    }

    public function actionCreate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $currentDate = new \DateTime();
        $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
        $prs = ProformaInvoice::find()->notDeleted()->location($this->locationId)->all();
        foreach ($prs as $pr) {
            $pr->updateAttributes(['isDeleted' => true]);
        } 
        $location = Location::findOne($this->locationId);
        Console::output("processing:  " . $location->name . '   creating payment request', Console::FG_GREEN, Console::BOLD);         
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->location($this->locationId)
            ->privateProgram()
            ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
            ->isRegular()
            ->joinWith(['course' => function ($query) use ($priorDate) { 
                $query->andWhere(['>=', 'DATE(course.endDate)', $priorDate])
                        ->confirmed()
                        ->notDeleted();
            }])
            ->notPaymentPrefered()
            ->all();
        Console::startProgress(0, 'Creating Payment Request...');
        foreach ($enrolments as $enrolment) {
            Console::output("processing:  " . $enrolment->id . '   creating payment request', Console::FG_GREEN, Console::BOLD);    
            $dateRange = $enrolment->getCurrentPaymentCycleDateRange($priorDate);
            $enrolment->createPaymentRequest($dateRange);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
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