<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceLineItemController extends Controller {

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

    public function actionEdit($id){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->post('hasEditable')) {
            $lineItemIndex = Yii::$app->request->post('editableIndex');
            $model = InvoiceLineItem::findOne(['id' => $id]);
            $result = [
				'output' => '',
				'message' => ''
			];
            $post = Yii::$app->request->post();
            if( ! empty($post['InvoiceLineItem'][$lineItemIndex]['description'])){
				$model->description = $post['InvoiceLineItem'][$lineItemIndex]['description'];
                $output = $model->description;
            }

            $model->save();
            $result = [
				'output' => $output,
				'message' => ''
			];
            return $result;
        }
    }
}
				