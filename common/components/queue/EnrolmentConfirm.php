<?php

namespace common\components\queue;

use common\models\Course;
use common\models\Enrolment;
use Yii;
use common\models\User;
use yii\helpers\Console;
use common\models\log\StudentLog;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

/**
 * Class OderNotification.
 */
class EnrolmentConfirm extends BaseObject implements RetryableJobInterface
{
    public $enrolmentId;
    public $userId;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $loggedUser = User::findOne(['id' => $this->userId]);
        Yii::$app->user->setIdentity($loggedUser);
        $enrolmentModel = Enrolment::findOne(['id' => $this->enrolmentId]);
        $enrolmentModel->isConfirmed = true;
        $enrolmentModel->save();
        $enrolmentModel->setPaymentCycle($enrolmentModel->enrolmentPaymentFrequency->paymentCycleStartDate);
        if ($enrolmentModel->course->isPrivate()) {
            $enrolmentModel->course->updateDates();
        }
        $enrolmentModel->on(
            Enrolment::EVENT_AFTER_INSERT,
            [new StudentLog(), 'addEnrolment'],
            ['loggedUser' => $loggedUser]
        );
        $enrolmentModel->customer->updateCustomerBalance();
        $enrolmentModel->setDueDate();
        return true;
    }
    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }
    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 1;
    }
}
