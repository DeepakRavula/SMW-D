<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\ItemType;

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

    public function actionEdit($id)
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		if (Yii::$app->request->post('hasEditable')) {
			$lineItemIndex	 = Yii::$app->request->post('editableIndex');
			$model			 = InvoiceLineItem::findOne(['id' => $id]);
			$result			 = [
				'output' => '',
				'message' => ''
			];
			$post			 = Yii::$app->request->post();
			if (!empty($post['InvoiceLineItem'][$lineItemIndex]['description'])) {
				$model->description	 = $post['InvoiceLineItem'][$lineItemIndex]['description'];
				$output				 = $model->description;
				$model->save();
			}
			if (!empty($post['InvoiceLineItem'][$lineItemIndex]['amount'])) {
				$model->amount	 = $post['InvoiceLineItem'][$lineItemIndex]['amount'];
				$output			 = $model->amount;
				$model->save();
				$model->invoice->subTotal	 = $model->invoice->lineItemTotal;
				$model->invoice->total		 = $model->invoice->subTotal + $model->invoice->tax;
				$model->invoice->save();
			}
			$result = [
				'output' => $output,
				'message' => ''
			];
			return $result;
		}
	}
}
				