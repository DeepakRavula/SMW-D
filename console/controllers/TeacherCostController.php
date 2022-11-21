<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Qualification;
use common\models\User;
use common\models\InvoiceLineItem;

class TeacherCostController extends Controller
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
            $actionID == 'double-teacher-cost' || 'fix-line-item-cost' ? ['locationId'] : []
        );
    }

    public function actionDoubleTeacherCost()
    {
        $qualifications = Qualification::find()
                    ->notDeleted()
                    ->location($this->locationId)
                    ->all();
        foreach ($qualifications as $qualification) {
            $qualification->updateAttributes([
                'rate' => 2 * $qualification->rate
            ]);
        }
    }

    public function actionFixLineItemCost()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationId = $this->locationId;
        $lineItems = InvoiceLineItem::find()
                    ->notDeleted()
                    ->lessonItem()
                    ->joinWith(['invoice' => function ($query) use($locationId) {
                        $query->notDeleted()
                            ->location($locationId);
                    }])
                    ->all();
        foreach ($lineItems as $lineItem) {
            if ($lineItem->lesson) {
                $teacherCost = $lineItem->lesson->getTeacherCost();
                if ((float) $lineItem->unit * $teacherCost != (float) $lineItem->cost) {
                    $lineItem->updateAttributes([
                        'cost' => $lineItem->unit * $teacherCost
                    ]);
                }
            }
        }
    }
}