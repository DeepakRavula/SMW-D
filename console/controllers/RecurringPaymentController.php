<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Enrolment;
use common\models\Payment;
use common\models\User;
use common\models\Lesson;
use common\models\LessonPayment;
use common\models\Location;
use common\models\CustomerRecurringPayment;
use common\models\CustomerRecurringPaymentEnrolment;
class RecurringPaymentController extends Controller
{
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
        $locationIds = [];
        $locations = Location::find()->notDeleted()->cronEnabledLocations()->all();
        foreach ($locations as $location) {
            $locationIds[] = $location->id;
        }
        $currentDate = new \DateTime();
        $priorDate = $currentDate->modify('+ 15 days')->format('Y-m-d');
        $recurringPaymentEnrolments = CustomerRecurringPayment::find()->all();
        foreach ($recurringPaymentEnrolments as $recurringPaymentEnrolment) {
            print_r("\n".$recurringPaymentEnrolment->customerRecurringPaymentenrolment);
        }
        die('coming');
        return true;
    }
}
