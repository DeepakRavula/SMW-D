<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Payment;
use yii\helpers\Console;

class PaymentController extends Controller
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
            $actionID == 'update-balance' ? ['locationId'] : []
        );
    }

    public function actionUpdateBalance()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        Console::startProgress(0, 'Updating Payment balance...');
        $payments = Payment::find()
            ->location($this->locationId)
            ->notDeleted()
            ->all();
        foreach ($payments as $payment) {
            Console::output("processing:  " . $payment->id . '   updating payment', Console::FG_GREEN, Console::BOLD);
            $payment->updateAttributes(['balance' => $payment->getBalanceAmount()]);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }
}