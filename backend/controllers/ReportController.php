<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\ReportSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;

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
		$searchModel = new ReportSearch();
		$currentDate = new \DateTime();
		$searchModel->fromDate = $currentDate->format('1-m-Y');
		$searchModel->toDate = $currentDate->format('t-m-Y');
		$searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
		$request = Yii::$app->request;
		if ($searchModel->load($request->get())) {
			$royaltyRequest = $request->get('ReportSearch');
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
			'royaltyPayment' => $royaltyPayment,
		]);
	}

	public function actionTaxCollected() {
		$searchModel = new ReportSearch();
		$currentDate = new \DateTime();
		$searchModel->fromDate = $currentDate->format('1-m-Y');
		$searchModel->toDate = $currentDate->format('t-m-Y');
		$searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
		$request = Yii::$app->request;
		if ($searchModel->load($request->get())) {
			$royaltyRequest = $request->get('ReportSearch');
			$searchModel->dateRange = $royaltyRequest['dateRange'];
			$searchModel->summarizeResults = $royaltyRequest['summarizeResults']; 
		}
		$toDate = $searchModel->toDate;
		if ($toDate > $currentDate) {
			$toDate = $currentDate;
		}
		$locationId = Yii::$app->session->get('location_id');
		$invoiceTaxes = InvoiceLineItem::find()
			->joinWith(['invoice' => function($query) use($locationId, $searchModel) {
				$query->andWhere([
					'location_id' => $locationId,
					'type' => Invoice::TYPE_INVOICE,
					'status' => [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT],
				])	
				->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
				->notDeleted();
			}])
			->andWhere(['>', 'tax_rate', 0]);
			if($searchModel->summarizeResults) {
				$invoiceTaxes->groupBy('DATE(invoice.date)');	
			} else {
				$invoiceTaxes->orderBy(['invoice.date' => SORT_ASC]);
			}

		$taxDataProvider = new ActiveDataProvider([
			'query' => $invoiceTaxes, 
		]);
				
		return $this->render('tax-collected', [
			'searchModel' => $searchModel, 
			'taxDataProvider' => $taxDataProvider,
		]);
	}
}
