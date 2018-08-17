<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Invoice;
use common\models\User;
use common\models\Lesson;

class InvoiceController extends Controller
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
            $actionID == 'trigger-save' ? ['locationId'] : []
        );
    }

    public function actionAllCompletedLessons()
    {
        $query = Lesson::find()
                ->location([14, 15, 16])
                ->isConfirmed()
                ->notDeleted();
        $privateLessons = $query->completedUnInvoicedPrivate()->all();
        $groupLessons = $query->groupLessons()->completed()->all();
        foreach ($groupLessons as $lesson) {
            foreach ($lesson->enrolments as $enrolment) {
                if (!$enrolment->hasInvoice($lesson->id)) {
                    $lesson->createGroupInvoice($enrolment->id);
                }
            }
        }
        foreach ($privateLessons as $lesson) {
            if (!$lesson->hasInvoice()) {
                $lesson->createPrivateLessonInvoice();
            }
        }

        return true;
    }

    public function actionAllExpiredLessons()
    {
        $lessons = Lesson::find()
            ->privateLessons()
            ->location([14, 15, 16])
			->isConfirmed()
            ->notDeleted()
            ->unscheduled()
            ->notRescheduled()
            ->expired()
            ->all();
        try {
            foreach($lessons as $lesson) {
                $lesson->createPrivateLessonInvoice();
            }
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
        }

        return true;
    }
    
    public function actionTriggerSave()
    {
        $locationId = $this->locationId;
        $invoices = Invoice::find()
                    ->notDeleted()
                    ->location($locationId)
                    ->all();
        foreach ($invoices as $invoice) {
            $invoice->save();
        }
    }
}