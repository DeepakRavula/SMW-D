<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\EmailForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use common\models\Invoice;

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
		$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
		$location = Location::findOne(['id' => $locationId]);
		$model = new EmailForm();
        if($model->load(Yii::$app->request->post())) {
            $content = [];
            foreach($model->to as $email) {
                $content[] = Yii::$app->mailer->compose('content', [
                    'content' => $model->content,
                ])
				->setFrom(Yii::$app->params['robotEmail'])
				->setReplyTo($location->email)
				->setTo($email)
				->setSubject($model->subject);
			}
            Yii::$app->mailer->sendMultiple($content);
			$data = null;
			if(!empty($model->id)) {
				$invoice = Invoice::findOne(['id' => $model->id]);
				$invoice->isSent = true;
				$invoice->save();
				$data = $this->renderAjax('/invoice/_show-all',[
					'model' => $invoice,		
				]);
			}
			return [
				'status' => true,
				'message' => 'Mail has been sent successfully',
				'data' => $data
			];
        }
	}
}
