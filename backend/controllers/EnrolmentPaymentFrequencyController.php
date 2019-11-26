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
use Carbon\Carbon;
use common\models\AutoRenewal;

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
        $enrolmentPaymentFrequency = new EnrolmentPaymentFrequency();
        $enrolmentPaymentFrequency->enrolmentId = $id;
        $enrolmentPaymentFrequency->effectiveDate =  (new \DateTime())->format('M d,Y');
        $data = $this->renderAjax('_form', [
            'model' => $model, 
            'enrolmentPaymentFrequency' => $enrolmentPaymentFrequency,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        } else {
              $oldPaymentFrequency = clone $model;
              $model->load($post);  
              $enrolmentPaymentFrequency->load($post);
              $course = $model->course;
              $effectiveDate = Carbon::parse($enrolmentPaymentFrequency->effectiveDate);
              $lastPaymentCycleEndDate = Carbon::parse($effectiveDate)->addMonthsNoOverflow($model->paymentFrequencyId)->format('Y-m-d');
              $courseEndDate = Carbon::parse($course->endDate)->format('Y-m-d');
              if ($courseEndDate < $lastPaymentCycleEndDate) {
                    $autoRenew = new AutoRenewal();
                    $autoRenew->renewEnrolment($model->course);       
                if ($model->save()) {
                    $enrolmentPaymentFrequency->resetPaymentCycle();
            }                              
        } else {
            if ($model->save()) {
                $enrolmentPaymentFrequency->resetPaymentCycle();
        }                        
              
        }
      
    
    return [
        'status' => true,
    ];
    $isAlreadyPosted = true;
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
