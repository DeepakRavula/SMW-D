<?php

namespace backend\controllers;

use backend\models\EmailForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use common\models\Invoice;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use common\models\Location;
/**
 * BlogController implements the CRUD actions for Blog model.
 */
class EmailController extends BaseController
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
           	'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['send'],
                        'roles' => ['administrator', 'staffmember', 'owner'],
                    ],
                ],
            ], 
        ];
    }
    public function actionSend()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $location = Location::findOne(['id' => $locationId]);
        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post())) {
            $content = [];
            foreach ($model->to as $email) {
                $content[] = Yii::$app->mailer->compose('content', [
                    'content' => $model->content,
                ])
                ->setFrom($location->email)
                ->setReplyTo($location->email)
                ->setTo($email)
                ->setSubject($model->subject);
            }
            Yii::$app->mailer->sendMultiple($content);
            $data = null;
            if (!empty($model->id)) {
                $invoice = Invoice::findOne(['id' => $model->id]);
                $invoice->isSent = true;
                $invoice->save();
                $data = $this->renderAjax('/invoice/_show-all', [
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
