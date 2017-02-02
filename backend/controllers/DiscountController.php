<?php

namespace backend\controllers;

use Yii;
use common\models\PaymentFrequency;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
/**
 * DiscountController implements the CRUD actions for Discount model.
 */
class DiscountController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Updates an existing PaymentFrequencyDiscount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionEdit()
    {
		$paymentFrequencies = PaymentFrequency::find()->all();
		$request = Yii::$app->request;
		if(!empty($request->post())) {
			Model::loadMultiple($paymentFrequencies, $request->post());
			foreach ($paymentFrequencies as $paymentFrequency) {
				$paymentFrequency->paymentFrequencyDiscount->value = $paymentFrequency->individualDiscountValue;
				$paymentFrequency->familyDiscount->value = $paymentFrequency->familyDiscountValue;
				$paymentFrequency->paymentFrequencyDiscount->save();
				$paymentFrequency->familyDiscount->save();	
				Yii::$app->session->setFlash('alert', [
               	 	'options' => ['class' => 'alert-success'],
                	'body' => 'Discount has been updated successfully',
            	]);
			}
		}
		return $this->render('update', [
			'paymentFrequencies' => $paymentFrequencies,
		]);
    }

    /**
     * Finds the PaymentFrequencyDiscount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PaymentFrequencyDiscount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Discount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
