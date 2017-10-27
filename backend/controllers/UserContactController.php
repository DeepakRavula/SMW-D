<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\EmailForm;
use Yii;
use common\models\UserEmail;
use common\models\UserContact;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class UserContactController extends Controller
{
    public function behaviors()
    {
        return [
			'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['create-email'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
            
        ];
    }
	public function actionCreateEmail($id)
	{
		$email = new UserEmail();
		$contact = new UserContact();
		$email->load(Yii::$app->request->post());
		$contact->load(Yii::$app->request->post());
		$contact->userId = $id;
		$contact->isPrimary = false;
		if($contact->save()) {
			$email->userContactId = $contact->id;
			$email->save();
			return [
				'status' => true,
			];
		}
	}
}
