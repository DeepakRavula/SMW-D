<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

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

	public function actionDelete($id){
		$model = $this->findModel($id);
		$model->invoice->subTotal -= $model->amount;
		$model->invoice->tax -= $model->tax_rate;
		$model->invoice->total = $model->invoice->subTotal + $model->invoice->tax;
		$model->invoice->save();
		$model->delete();
		Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Line Item has been deleted successfully'
			]);
		return $this->redirect(['invoice/view', 'id' => $model->invoice->id]);
	}

	protected function findModel($id) {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = InvoiceLineItem::find()
				->joinWith(['invoice' => function($query) use($locationId) {
					$query->where(['location_id' => $locationId]);
				}])
				->where([
					'invoice_line_item.id' => $id,
				])
				->one();
		if ($model !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
				