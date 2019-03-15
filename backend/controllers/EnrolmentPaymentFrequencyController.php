<?php

namespace backend\controllers;


use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use common\models\Enrolment;
use common\models\Student;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use common\models\EnrolmentPaymentFrequency;

class EnrolmentPaymentFrequencyController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['change-payment-frequency'],
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
                        'actions' => ['change-payment-frequency'],
                        'roles' => ['administrator', 'staffmember', 'owner'],
                    ],
                ],
            ], 
        ];
    }
   
    
    public function actionChangePaymentFrequency($id)
    {
        $model = $this->findModel($id);

        $data = $this->renderAjax('_form', [
            'model' => $model, 
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        } else {
              $oldPaymentFrequency = clone $model;
              $enrolmentPaymentFrequency = new EnrolmentPaymentFrequency();
              $enrolmentPaymentFrequency->enrolmentId = $model->id;
              $model->load($post);  
              if ($model->save()) {
                if ((int) $oldPaymentFrequency->paymentFrequencyId !== (int) $model->paymentFrequencyId) {
                    $enrolmentPaymentFrequency->resetPaymentCycle();
                }
            }
            return [
                'status' => true,
            ];
        }
}

protected function findModel($id)
{
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $model = Enrolment::find()->location($locationId)->isRegular()
        ->notDeleted()
        ->andWhere(['enrolment.id' => $id])->one();
    if ($model !== null) {
        return $model;
    } else {
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
}
