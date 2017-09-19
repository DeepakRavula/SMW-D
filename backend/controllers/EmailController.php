<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\EmailForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class EmailController extends Controller
{
    public function behaviors()
    {
        return [
			'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['send'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
            
        ];
    }
	public function actionSend()
	{
		$locationId = Yii::$app->session->get('location_id');
		$location = Location::findOne(['id' => $locationId]);
		$model = new EmailForm();
        if($model->load(Yii::$app->request->post())) {
            $content = [];
            foreach($model->to as $email) {
                $content[] = Yii::$app->mailer->compose('lesson-reschedule', [
                    'content' => $model->content,
                ])
				->setFrom(Yii::$app->params['robotEmail'])
				->setReplyTo($location->email)
				->setTo($email)
				->setSubject($model->subject);
			}
            Yii::$app->mailer->sendMultiple($content);
			return [
				'status' => true,
				'message' => 'Mail has been sent successfully',
			];
        }
	}
}
