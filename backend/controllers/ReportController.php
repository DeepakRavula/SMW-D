<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentSearch;
use backend\models\search\ReportSearch;
use backend\models\search\StudentBirthdaySearch;
use backend\models\search\InvoiceLineItemSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;
use common\models\PaymentMethod;
use backend\models\search\DiscountSearch;


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
    public function actionStudentBirthday() {
         $searchModel = new StudentBirthdaySearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('d-m-Y');
		$nextSevenDate = $currentDate->modify('+7days');
        $searchModel->toDate = $nextSevenDate->format('d-m-Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $studentBirthdayRequest = $request->get('StudentBirthdaySearch');
            $searchModel->dateRange = $studentBirthdayRequest['dateRange'];
        }
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('student-birthday/index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
	public function actionPayment()
    {
        $searchModel = new PaymentSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M d,Y');
        $searchModel->toDate = $currentDate->format('M d,Y');
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $paymentRequest = $request->get('PaymentSearch');
            $searchModel->dateRange = $paymentRequest['dateRange'];
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('payment/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
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
			->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->notDeleted()
			->sum('tax');

		$payments = Payment::find()
			->joinWith(['invoice i' => function ($query) use ($locationId) {
				$query->where(['i.location_id' => $locationId]);
			}])
			->andWhere(['NOT', ['payment_method_id' => [PaymentMethod::TYPE_CREDIT_USED, PaymentMethod::TYPE_CREDIT_APPLIED]]])
            ->notDeleted()
			->andWhere(['between', 'payment.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->sum('payment.amount');

		$royaltyPayment = InvoiceLineItem::find()
			->joinWith(['invoice i' => function ($query) use ($locationId) {
					$query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
				}])
			->andWhere(['between', 'i.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
			->royaltyFree()
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
				
		return $this->render('tax-collected/index', [
			'searchModel' => $searchModel, 
			'taxDataProvider' => $taxDataProvider,
		]);
	}

	public function actionRoyaltyFree() {
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
		$royaltyFreeItems = InvoiceLineItem::find()
			->joinWith(['invoice' => function($query) use($locationId, $searchModel) {
				$query->andWhere([
					'location_id' => $locationId,
					'type' => Invoice::TYPE_INVOICE,
				])	
				->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
				->notDeleted();
			}])
			->royaltyFree();

		$royaltyFreeDataProvider = new ActiveDataProvider([
			'query' => $royaltyFreeItems, 
		]);
				
		return $this->render('royalty-free-item', [
			'searchModel' => $searchModel, 
			'royaltyFreeDataProvider' => $royaltyFreeDataProvider,
		]);
	}

    public function actionItems()
    {
        $searchModel              = new InvoiceLineItemSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M d,Y');
        $searchModel->toDate = $currentDate->format('M d,Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $invoiceLineItemRequest = $request->get('InvoiceLineItemSearch');
            $searchModel->dateRange = $invoiceLineItemRequest['dateRange'];
        }
        $searchModel->groupByItem = true;
        $dataProvider             = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('item/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionItemCategory()
    {
        $searchModel                      = new InvoiceLineItemSearch();
        $searchModel->groupByItemCategory = true;
        $searchModel->fromDate            = (new \DateTime())->format('M d,Y');
        $searchModel->toDate              = (new \DateTime())->format('M d,Y');
        $searchModel->dateRange           = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $invoiceLineItemRequest = $request->get('InvoiceLineItemSearch');
            $searchModel->dateRange = $invoiceLineItemRequest['dateRange'];
        }
        $dataProvider                     = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('item-category/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionDiscount()
    {
        $searchModel              = new DiscountSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M 1,Y');
        $searchModel->toDate = $currentDate->format('M t,Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $discountRequest = $request->get('DiscountSearch');
            $searchModel->dateRange = $discountRequest['dateRange'];
        }
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('discount/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDiscountPrint()
    {
        $searchModel              = new DiscountSearch();
        if ($searchModel->load(Yii::$app->request->get())) {
            $discountRequest = Yii::$app->request->get('DiscountSearch');
            $searchModel->dateRange = $discountRequest['dateRange'];
        }
        $dataProvider             = $searchModel->search(Yii::$app->request->queryParams);

        $this->layout             = '/print';

        return $this->render('/report/discount/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    public function actionTaxCollectedPrint()
    {
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
        $invoiceTaxes = InvoiceLineItem::find()
            ->joinWith(['invoice' => function($query) use($locationId, $searchModel) {
                    $query->andWhere([
                        'location_id' => $locationId,
                        'type' => Invoice::TYPE_INVOICE,
                    ])
                    ->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->notDeleted();
                }])
            ->andWhere(['>', 'tax_rate', 0]);
        if ($searchModel->summarizeResults) {
            $invoiceTaxes->groupBy('DATE(invoice.date)');
        } else {
            $invoiceTaxes->orderBy(['invoice.date' => SORT_ASC]);
        }

        $taxDataProvider = new ActiveDataProvider([
            'query' => $invoiceTaxes,
        ]);
        $this->layout = '/print';
        return $this->render('/report/tax-collected/_print', [
                'searchModel' => $searchModel,
                'taxDataProvider' => $taxDataProvider,
        ]);
    }
}
