<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\RoyaltySearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\InvoiceLineItem;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class ReportController extends Controller {

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

	public function actionRoyalty() {
		$searchModel = new RoyaltySearch();
		$currentDate = new \DateTime();
		$searchModel->fromDate = $currentDate->format('1-m-Y');
		$searchModel->toDate = $currentDate->format('t-m-Y');
		$searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
		$request = Yii::$app->request;
		if ($searchModel->load($request->get())) {
			$royaltyRequest = $request->get('RoyaltySearch');
			$searchModel->dateRange = $royaltyRequest['dateRange'];
		}
		$toDate = $searchModel->toDate;
		if ($toDate > $currentDate) {
			$toDate = $currentDate;
		}
		$locationId = Yii::$app->session->get('location_id');
		
		$invoiceTaxTotal = Invoice::find()
			->where(['location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE])
			->andWhere(['NOT', ['status' => Invoice::STATUS_OWING]])
			->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->notDeleted()
			->sum('tax');

		$payments = Payment::find()
			->joinWith(['invoice i' => function ($query) use ($locationId) {
					$query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
				}])
			->andWhere(['between', 'payment.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->sum('payment.amount');

		$royaltyPayment = InvoiceLineItem::find()
			->joinWith(['invoice i' => function ($query) use ($locationId) {
					$query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
					$query->andWhere(['NOT', ['status' => Invoice::STATUS_OWING]]);
				}])
			->andWhere(['between', 'i.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->andWhere(['invoice_line_item.isRoyalty' => false])
			->sum('invoice_line_item.amount');
				
		return $this->render('royalty', [
			'searchModel' => $searchModel, 
			'invoiceTaxTotal' => $invoiceTaxTotal,
			'payments' => $payments,
			'royaltyPayment' => $royaltyPayment
		]);
	}

}
