<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;

class CustomerController extends Controller
{
    public $locationId;

    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function actionSetStatusActive()
    {
        $customers = User::find()
            ->allCustomers()
            ->notDeleted()
            ->all();
        foreach ($customers as $customer) {
            $customer->updateAttributes(['status' => User::STATUS_ACTIVE]);
        }
    }

    public function actionSetStatus()
    {
        $customers = User::find()
            ->allCustomers()
            ->notDeleted()
            ->all();
        foreach ($customers as $customer) {
            $customer->setActiveStatus();
        }
    }
}