<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentReportSearch;
use backend\models\search\ReportSearch;
use backend\models\search\StudentBirthdaySearch;
use backend\models\search\InvoiceLineItemSearch;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;
use common\models\PaymentMethod;
use backend\models\search\DiscountSearch;
use common\models\Location;
use common\models\User;
use common\components\controllers\BaseController;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use common\models\Lesson;
use common\models\CustomerAccount;
use common\models\Enrolment;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class ReportController extends BaseController
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
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['student-birthday'],
                        'roles' => ['manageBirthdays'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['payment'],
                        'roles' => ['managePaymentsReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['sales-and-payment'],
                        'roles' => ['manageSalesAndPayment'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['royalty'],
                        'roles' => ['manageRoyalty'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['tax-collected'],
                        'roles' => ['manageTaxCollected'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['royalty-free'],
                        'roles' => ['manageRoyaltyFreeItems'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['items'],
                        'roles' => ['manageItemReport'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['customer-items'],
                        'roles' => ['manageItemsByCustomer'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['discount', 'discount-print'],
                        'roles' => ['manageDiscountReport'],
                    ],
					[
                        'allow' => true,
                        'actions' => ['all-locations'],
                        'roles' => ['manageAllLocations'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['item-category'],
                        'roles' => ['manageItemCategoryReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['account-receivable'],
                        'roles' => ['manageAccountReceivableReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['financial-summary-report'],
                        'roles' => ['manageAccountReceivableReport'],
                    ],
                ],
            ], 
        ];
    }

    public function actionStudentBirthday()
    {
        $searchModel = new StudentBirthdaySearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $nextSevenDate = $currentDate->modify('+7days');
        $searchModel->toDate = Yii::$app->formatter->asDate($nextSevenDate);
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
        $searchModel = new PaymentReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M d,Y');
        $searchModel->toDate = $currentDate->format('M d,Y');
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $request = Yii::$app->request;
        $searchModel->load($request->get());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $paymentsAmount = Payment::find()
            ->exceptAutoPayments()
            ->exceptGiftCard()
            ->location($locationId)
            ->notDeleted()
            ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), 
                (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->sum('payment.amount');
        return $this->render('payment/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'paymentsAmount' => $paymentsAmount
        ]);
    }
    
    public function actionRoyalty()
    {
        $searchModel = new ReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->toDate = Yii::$app->formatter->asDate($currentDate);
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        
        $invoiceTaxTotal = Invoice::find()
            ->andWhere(['location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE])
            ->andWhere(['between', 'DATE(date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->notDeleted()
            ->sum('tax');

        $payments = Payment::find()
            ->exceptAutoPayments()
            ->exceptGiftCard()
            ->location($locationId)
            ->notDeleted()
            ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->sum('payment.amount');
        $giftCardPayments = Payment::find()
            ->giftCardPayments()
            ->location($locationId)
            ->notDeleted()
            ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->sum('payment.amount');
        $fromDate = new \DateTime($searchModel->fromDate);
        $toDate = new \DateTime($searchModel->toDate);

        $royaltyFreeItems = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $fromDate, $toDate) {
                $query->location($locationId)
                    ->invoice()
                    ->notDeleted()
                    ->andWhere(['between', 'DATE(invoice.date)', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            }])
            ->royaltyFree()
            ->all();
            $royaltyFreeAmount = 0;
            foreach ($royaltyFreeItems as $royaltyFreeItem) {
                $royaltyFreeAmount += $royaltyFreeItem->netPrice;
                
            }
                
        return $this->render('royalty/index', [
            'searchModel' => $searchModel,
            'invoiceTaxTotal' => $invoiceTaxTotal,
            'payments' => $payments,
            'royaltyFreeAmount' => $royaltyFreeAmount,
            'giftCardPayments' => $giftCardPayments,
        ]);
    }

    public function actionTaxCollected()
    {
        $searchModel = new ReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->toDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $request = Yii::$app->request;
        $searchModel->load($request->get());
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $invoiceTaxes = Invoice::find()
            ->notDeleted()
            ->location($locationId)
            ->invoice()
            ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), 
                (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->orderBy(['invoice.date' => SORT_ASC]);
            
        $taxSum = $invoiceTaxes->sum('tax');
        $subtotalSum = $invoiceTaxes->sum('subTotal');
        $totalSum = $invoiceTaxes->sum('total');

        $taxDataProvider = new ActiveDataProvider([
            'query' => $invoiceTaxes,
            'pagination' => false
        ]);
                
        return $this->render('tax-collected/index', [
            'searchModel' => $searchModel,
            'taxDataProvider' => $taxDataProvider,
            'taxSum' => $taxSum,
            'subtotalSum' => $subtotalSum,
            'totalSum' => $totalSum
        ]);
    }

    public function actionRoyaltyFree()
    {
        $searchModel = new ReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->toDate = Yii::$app->formatter->asDate($currentDate);
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $royaltyFreeItems = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $searchModel) {
                $query->location($locationId)
                ->invoice()
                ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
                ->notDeleted();
            }])
            ->royaltyFree();

        $royaltyFreeDataProvider = new ActiveDataProvider([
            'query' => $royaltyFreeItems,
        ]);
                
        return $this->render('royalty-free-item/index', [
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

        return $this->render(
            'item/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]
        );
    }
    
    public function actionCustomerItems()
    {
        $currentYearFirstDate = new \DateTime('first day of January');
        $currentYearLastDate  = new \DateTime('last day of December');
        $searchModel                   = new InvoiceLineItemSearch();
        $searchModel->fromDate         = $currentYearFirstDate->format('M d,Y');
        $searchModel->toDate           = $currentYearLastDate->format('M d,Y');
        $searchModel->dateRange        = $searchModel->fromDate.' - '.$searchModel->toDate;
        $searchModel->customerId       = null;
        $searchModel->isCustomerReport = true;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $invoiceLineItemRequest = $request->get('InvoiceLineItemSearch');
            $searchModel->dateRange = $invoiceLineItemRequest['dateRange'];
            if (!empty($invoiceLineItemRequest['customerId'])) {
                $searchModel->customerId = $invoiceLineItemRequest['customerId'];
            }
            if (!empty($invoiceLineItemRequest['isCustomerReport'])) {
                $searchModel->isCustomerReport = $invoiceLineItemRequest['isCustomerReport'];
            }
        }
        $dataProvider             = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render(
            'customer-item/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]
        );
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
            $searchModel->category = $invoiceLineItemRequest['category'];
        }
        $dataProvider                     = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render(
            'item-category/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]
        );
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

        return $this->render(
            'discount/index',
                [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]
        );
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
    public function actionAllLocations()
    {
        $searchModel = new ReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->toDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->dateRange = (new \DateTime('first day of previous month'))->format('M d,Y') . ' - ' . (new \DateTime('last day of previous month'))->format('M d,Y');
        $request = Yii::$app->request;
        $searchModel->load($request->get());
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $defaultLocation = Location::findOne(1);
        $locations = Location::find()->notDeleted()->andWhere(['NOT', ['id' => $defaultLocation->id]])->all();
        foreach ($locations as $location) {
            $results[] = $location->getLocationDetails($searchModel->fromDate, $searchModel->toDate);
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['locationId', 'locationName', 'activeEnrolmentsCount', 'revenue', 'locationDebtValueRoyalty',  'locationDebtValueAdvertisement','total', 'taxAmount']
            ],
            'pagination' => false
        ]);
        return $this->render('all-locations/index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSalesAndPayment()
    {
        $searchModel = new ReportSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->toDate = Yii::$app->formatter->asDate($currentDate);
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;     
        $request = Yii::$app->request;
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        if ($searchModel->load($request->get())) {
            $reportRequest = $request->get('ReportSearch');
            $searchModel->dateRange = $reportRequest['dateRange']; 
        }
        $salesQuery = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $searchModel) {
            $query->notDeleted()
                ->notCanceled()
                ->notReturned()
                ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE])
                ->location($locationId)
                ->between((new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d'))
                ->orderBy([
                        'DATE(invoice.date)' => SORT_ASC,
                    ]);
            }])
            ->joinWith(['itemCategory' => function ($query) {
                $query->groupBy('item_category.id');
            }]);
           

        $salesDataProvider = new ActiveDataProvider([
            'query' => $salesQuery,
        ]);   
            $paymentsQuery = Payment::find()
                ->exceptAutoPayments()
                ->exceptGiftCard()
                ->location($locationId)
                ->notDeleted()
                ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), 
                    (new \DateTime($searchModel->toDate))->format('Y-m-d')])
                ->groupBy('payment.payment_method_id');    
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $paymentsQuery,
        ]);       
        
                return $this->render(
                    'sales-and-payment/index',
                        [
                        'searchModel' => $searchModel,
                        'salesDataProvider' => $salesDataProvider,
                        'paymentsDataProvider' => $paymentsDataProvider,
                ]
                );
    }

    public function actionAccountReceivable()
    {  
        $searchModel = new ReportSearch();
        $searchModel->showAllActive = true;
        $searchModel->showAllInActive = true;
        if (Yii::$app->request->queryParams) {
            $searchModel->load(Yii::$app->request->queryParams);
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $customersInclude = [];
        $customers = User::find()
                ->customers($locationId)
                ->notDeleted();
               if ($searchModel->showAllInActive && !$searchModel->showAllActive) {
                    $customers->Inactive();
                } else if ($searchModel->showAllActive && !$searchModel->showAllInActive ) {
                    $customers->active();  
                } else if (!$searchModel->showAllActive && !$searchModel->showAllInActive ) { 
                    $customers->andWhere(['user.id' => null]);
                }
                $customers = $customers->all();
        foreach ($customers as $customer) {
            if ($customer->customerAccountBalance() != '0.00') {
                $customersInclude[] = $customer->id;
            }
        }
        $customerslist = User::find()
                ->customers($locationId)
                ->joinWith(['userProfile' => function($query) {
                    $query->orderBy(['user_profile.firstname' => SORT_ASC]);
                }])
                ->andWhere(['IN', 'user.id', $customersInclude]);
        $dataProvider = new ActiveDataProvider([
            'query' => $customerslist,
        ]);
        return $this->render( 'account-receivable/index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
    }

    public function actionFinancialSummaryReport()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $currentDate = (new \DateTime())->format('Y-m-d');

        $paidFutureLessonsSearchModel = new ReportSearch();
        $request = Yii::$app->request;
        $searchRequest = $request->get('ReportSearch');
        $paidFutureLessonsSearchModel->goToDate = $currentDate;
        if (!empty($searchRequest['goToDate'])) {
            $paidFutureLessonsSearchModel->goToDate = $searchRequest['goToDate'];
        }

        $paidFutureLessondataProvider = $paidFutureLessonsSearchModel->search(Yii::$app->request->queryParams);

        $paidPastLessons = Lesson::find()
                        ->joinWith(['lessonPayments' => function ($query) {
                            $query->andWhere(['NOT', ['lesson_payment.lessonId' => null]]);
                        }])
                        ->andWhere(['<', 'lesson.date', $currentDate])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->location($locationId)
                        ->privateLessons()
                        ->notDeleted()
                        ->isConfirmed()
                        ->notCanceled()
                        ->unscheduled()
                        ->notExpired()
                        ->regular();
        $paidPastLessonsSum = $paidPastLessons->sum('lesson_payment.amount');
        $paidPastLessonsCount = $paidPastLessons->count();

        $activeOutstandingInvoices = Invoice::find()
                        ->invoice()
                        ->location($locationId)
                        ->andWhere(['>', 'invoice.balance', 0.0])
                        ->joinWith(['user' => function ($query) {
                            $query->active();
                        }])
                        ->notDeleted()
                        ->unpaid();
        $inactiveOutstandingInvoices = Invoice::find()
                        ->invoice()
                        ->location($locationId)
                        ->andWhere(['>', 'invoice.balance', 0.0])
                        ->joinWith(['user' => function ($query) {
                            $query->inactive();
                        }])
                        ->notDeleted()
                        ->unpaid();
        $activeOutstandingInvoicesSum = $activeOutstandingInvoices->sum('balance');
        $activeOutstandingInvoicesCount = $activeOutstandingInvoices->count();
        $inactiveOutstandingInvoicesSum = $inactiveOutstandingInvoices->sum('balance');
        $inactiveOutstandingInvoicesCount = $inactiveOutstandingInvoices->count();
        $activeCustomers = User::find()
                        ->customers($locationId)
                        ->owingCustomers()
                        ->excludeWalkin()
                        ->notDeleted();
        $numberOfActiveCustomers = $activeCustomers->count();
        $enrolments = Enrolment::find()
                        ->location($locationId)
                        ->notDeleted()
                        ->joinWith(['course' => function($query) {
                            $query->andWhere('DATE(course.endDate) >= CURRENT_DATE');
                        }])
                        ->isConfirmed()
                        ->isRegular();
        $numberOfEnrolments = $enrolments->count();

        $activeCustomersWithCredit = CustomerAccount::find()
                        ->location($locationId)
                        ->joinWith(['user' => function ($query) {
                            $query->active();
                        }])
                        ->andWhere(['<', 'balance', 0]);

        $inactiveCustomersWithCredit = CustomerAccount::find()
                        ->location($locationId)
                        ->joinWith(['user' => function ($query) {
                            $query->inactive();
                        }])
                        ->andWhere(['<', 'balance', 0]);

        $paidPastLessondataProvider = new ActiveDataProvider([
            'query' => $paidPastLessons,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        $activeInvoicedataProvider = new ActiveDataProvider([
            'query' => $activeOutstandingInvoices,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        $inactiveInvoicedataProvider = new ActiveDataProvider([
            'query' => $inactiveOutstandingInvoices,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        $activeCustomerWithCreditdataProvider = new ActiveDataProvider([
            'query' => $activeCustomersWithCredit,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        $inactiveCustomerWithCreditdataProvider = new ActiveDataProvider([
            'query' => $inactiveCustomersWithCredit,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render( 'financial-summary-report/index', [
                'paidFutureLessonsSearchModel' => $paidFutureLessonsSearchModel,
                'paidFutureLessondataProvider' => $paidFutureLessondataProvider,
                'paidPastLessondataProvider' => $paidPastLessondataProvider,
                'activeInvoicedataProvider' => $activeInvoicedataProvider,
                'inactiveInvoicedataProvider' => $inactiveInvoicedataProvider,
                'activeCustomerWithCreditdataProvider' => $activeCustomerWithCreditdataProvider,
                'inactiveCustomerWithCreditdataProvider' => $inactiveCustomerWithCreditdataProvider,
                'paidPastLessonsSum' => $paidPastLessonsSum,
                'activeOutstandingInvoicesSum' => $activeOutstandingInvoicesSum,
                'inactiveOutstandingInvoicesSum' => $inactiveOutstandingInvoicesSum,
                'paidPastLessonsCount' => $paidPastLessonsCount,
                'activeOutstandingInvoicesCount' => $activeOutstandingInvoicesCount,
                'inactiveOutstandingInvoicesCount' => $inactiveOutstandingInvoicesCount,
                'numberOfActiveCustomers' => $numberOfActiveCustomers,
                'numberOfEnrolments' => $numberOfEnrolments,
            ]);
    }
}
