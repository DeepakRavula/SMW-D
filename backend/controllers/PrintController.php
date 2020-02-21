<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use common\models\Payment;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use common\models\Course;
use common\models\Lesson;
use common\models\GroupLesson;
use common\models\ExamResult;
use common\models\Student;
use common\models\User;
use common\models\Location;
use backend\models\search\LessonSearch;
use backend\models\search\InvoiceSearch;
use backend\models\search\UserSearch;
use backend\models\search\PaymentFormSearch;
use common\models\CustomerAccount;
use common\models\CompanyAccount;
use backend\models\search\ReportSearch;
use common\models\PaymentMethod;
use backend\models\search\InvoiceLineItemSearch;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\ProformaInvoice;
use backend\models\search\ProformaInvoiceSearch;
use common\models\Receipt;
use common\models\PaymentReceipt;
use backend\models\PaymentForm;
use yii\data\ArrayDataProvider;
use common\models\InvoicePayment;
use backend\models\search\PaymentFormLessonSearch;
use backend\models\search\PaymentFormGroupLessonSearch;
use common\models\CustomerStatement;
use common\models\Enrolment;
use common\models\log\CustomerStatementLog;
use common\models\log\LogActivity;
use common\models\CourseSchedule;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class PrintController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'invoice', 'course', 'evaluation', 'teacher-lessons', 
                            'time-voucher', 'customer-invoice', 'account-view', 
                            'royalty', 'royalty-free', 'tax-collected', 'user', 
                            'customer-items-print', 'proforma-invoice','payment',
                            'receipt', 'sales-and-payment', 'customer-statement', 'all-locations', 'accounts-receivable','group-enrolment',
                        ],
                        'roles' => ['administrator', 'staffmember', 'owner'],
                    ],
                ],
            ],  
        ];
    }
    
    public function actionInvoice($id)
    {
        $model = Invoice::findOne(['id' => $id]);
        $invoiceLineItems = InvoiceLineItem::find()
                ->notDeleted()
                ->andWhere(['invoice_id' => $id]);
        $invoicePayments = InvoicePayment::find()
                 ->notDeleted()
                 ->joinWith(['payment' => function ($query) {
                 $query->notDeleted()
                  ->orderBy(['payment.date' => SORT_DESC]);
        }])
        ->invoice($id);
        if ($model->isProFormaInvoice()) {
            $invoicePayments->notCreditUsed();
        }
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
            'pagination' => false,
        ]);
        $invoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $invoicePayments,
        ]);
        $searchModel=new InvoiceSearch();
        $searchModel->toggleAdditionalColumns=false;
        $searchModel->isPrint = true;
        $searchModel->isMail = false;
        $this->layout = '/print';

        return $this->render('/invoice/print/view', [
            'model' => $model,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'searchModel'=>$searchModel,
        ]);
    }
    
    public function actionCourse($id)
    {
        $model = Course::findOne(['id' => $id]);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $model->id])
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
                'pagination' => false,
           ]);

        $this->layout = '/print';

        return $this->render('/course/_print', [
            'model' => $model,
            'lessonDataProvider' => $lessonDataProvider,
        ]);
    }
    
    public function actionEvaluation($studentId)
    {
        $studentModel = Student::findOne(['id' => $studentId]);
        $examResults = ExamResult::find()->notDeleted()->andWhere(['studentId' => $studentId]);
        $examResultDataProvider = new ActiveDataProvider([
            'query' => $examResults,
        ]);

        $this->layout = '/print';

        return $this->render('/student/exam-result/_print', [
            'studentModel' => $studentModel,
            'examResultDataProvider' => $examResultDataProvider,
        ]);
    }
    
    public function actionTeacherLessons($id)
    {
        $model = User::findOne(['id' => $id]);
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $request = Yii::$app->request;
        $lessonSearch = new LessonSearch();
        $lessonSearch->fromDate = new \DateTime();
        $lessonSearch->toDate = new \DateTime();
        $lessonSearchModel = $request->get('LessonSearch');
        
        if (!empty($lessonSearchModel)) {
            $lessonSearch->dateRange = $lessonSearchModel['dateRange'];
            list($lessonSearch->fromDate, $lessonSearch->toDate) = explode(' - ', $lessonSearch->dateRange);
            $lessonSearch->fromDate = new \DateTime($lessonSearch['fromDate']);
            $lessonSearch->toDate = new \DateTime($lessonSearch['toDate']);
	    $lessonSearch->summariseReport=$lessonSearchModel['summariseReport'];
        }
        $teacherLessons = Lesson::find()
            ->innerJoinWith('enrolment')
            ->location($locationId)
            ->andWhere(['lesson.teacherId' => $model->id])
            ->isConfirmed()
            ->notDeleted()
            ->scheduledOrRescheduled()
            ->between($lessonSearch->fromDate, $lessonSearch->toDate)
            ->orderBy(['date' => SORT_ASC]);
            if($lessonSearch->summariseReport) {
		$teacherLessons->groupBy(['DATE(lesson.date)']);
			} 
        $teacherLessonDataProvider = new ActiveDataProvider([
            'query' => $teacherLessons,
            'pagination' => false,
        ]);
        
        $this->layout = '/print';

        return $this->render('/user/teacher/_print', [
            'model' => $model,
            'teacherLessonDataProvider' => $teacherLessonDataProvider,
            'fromDate' => $lessonSearch->fromDate,
            'toDate' => $lessonSearch->toDate,
            'searchModel' => $lessonSearch
        ]);
    }
    
    public function actionTimeVoucher($id)
    {
        $model = User::findOne(['id' => $id]);
        $request = Yii::$app->request;
        $invoiceSearchModel = new InvoiceSearch();
        $invoiceSearchModel->dateRange = (new\DateTime())->format('M d,Y') . ' - ' . (new\DateTime())->format('M d,Y');
        if ($invoiceSearchModel->load($request->get())) {
            list($invoiceSearchModel->fromDate, $invoiceSearchModel->toDate) = explode(' - ', $invoiceSearchModel->dateRange);
        }
        $timeVoucher = InvoiceLineItem::find()
                        ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($invoiceSearchModel) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between((new\DateTime($invoiceSearchModel->fromDate))->format('Y-m-d'), (new\DateTime($invoiceSearchModel->toDate))->format('Y-m-d'));
            }])
            ->joinWith(['lesson' => function ($query) use ($model) {
                $query->andWhere(['lesson.teacherId' => $model->id])
                ->groupBy('lesson.id');
            }])
			->orderBy(['invoice.date' => SORT_ASC]);
            
        $timeVoucherDataProvider = new ActiveDataProvider([
            'query' => $timeVoucher,
            'pagination' => false,
        ]);
        
        $this->layout = '/print';

        return $this->render('/user/teacher/_print-time-voucher', [
            'model' => $model,
            'timeVoucherDataProvider' => $timeVoucherDataProvider,
            'fromDate' => $invoiceSearchModel->fromDate,
            'toDate' => $invoiceSearchModel->toDate,
            'searchModel' => $invoiceSearchModel,
        ]);
    }
    
    public function actionCustomerInvoice($id)
    {
        $model = User::findOne(['id' => $id]);
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $request = Yii::$app->request;
        $currentDate = new \DateTime();
        $model->fromDate = $currentDate->format('1-m-Y');
        $model->toDate = $currentDate->format('t-m-Y');
        $model->dateRange = $model->fromDate . ' - ' . $model->toDate;
        $userRequest = $request->get('User');
        if (!empty($userRequest)) {
            $model->dateRange = $userRequest['dateRange'];
            list($model->fromDate, $model->toDate) = explode(' - ', $userRequest['dateRange']);
            $invoiceStatus = $userRequest['invoiceStatus'];
            $studentId = $userRequest['studentId'];
        }
        $fromDate =  (new \DateTime($model->fromDate))->format('Y-m-d');
        $toDate =(new \DateTime($model->toDate))->format('Y-m-d');
        $invoiceQuery = Invoice::find()
                ->andWhere([
                    'invoice.user_id' => $model->id,
                    'invoice.type' => Invoice::TYPE_INVOICE,
                    'invoice.location_id' => $locationId,
                ])
                ->notDeleted()
                ->between($fromDate, $toDate);
        if (!empty($invoiceStatus) && (int)$invoiceStatus !== UserSearch::STATUS_ALL) {
            $invoiceQuery->andWhere(['invoice.status' => $invoiceStatus]);
        }
        if (!empty($studentId)) {
            $invoiceQuery->student($studentId);
        }
        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoiceQuery,
            'pagination' => false,
        ]);
        $this->layout = '/print';

        return $this->render('/user/customer/_print', [
            'model' => $model,
            'invoiceDataProvider' => $invoiceDataProvider,
            'dateRange' => $model->dateRange,
        ]);
    }
    
    public function actionAccountView($id, $accountView)
    {
        $model = User::findOne(['id' => $id]);
        if (!$accountView) {
            $accountQuery = CompanyAccount::find()
                    ->andWhere(['userId' => $id])
                    ->orderBy(['transactionId' => SORT_ASC]);
        } else {
            $accountQuery = CustomerAccount::find()
                    ->andWhere(['userId' => $id])
                    ->orderBy(['transactionId' => SORT_ASC]);
        }
        $accountDataProvider = new ActiveDataProvider([
            'query' => $accountQuery,
            'pagination' => false,
        ]);
        $this->layout = '/print';

        return $this->render('/user/customer/_accounts-print', [
                'model' => $model,
                'accountDataProvider' => $accountDataProvider,
                'userModel' => $model,
        ]);
    }
    
    public function actionRoyaltyFree()
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $royaltyFreeItems = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $searchModel) {
                $query->andWhere([
                        'location_id' => $locationId,
                        'type' => Invoice::TYPE_INVOICE,
                    ])
                    ->andWhere(['between', 'date', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
                    ->notDeleted();
            }])
            ->royaltyFree();

        $royaltyFreeDataProvider = new ActiveDataProvider([
            'query' => $royaltyFreeItems,
        ]);
        $this->layout = '/print';
        return $this->render('/report/royalty-free-item/_print', [
                'searchModel' => $searchModel,
                'royaltyFreeDataProvider' => $royaltyFreeDataProvider,
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
        $this->layout = '/print';       
        return $this->render('/report/tax-collected/_print', [
            'searchModel' => $searchModel,
            'taxDataProvider' => $taxDataProvider,
            'taxSum' => $taxSum,
            'subtotalSum' => $subtotalSum,
            'totalSum' => $totalSum
        ]);
    }
    
    public function actionRoyalty()
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;

        $invoiceTaxTotal = Invoice::find()
            ->andWhere(['location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE])
            ->andWhere(['between', 'date', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), (new \DateTime($searchModel->toDate))->format('Y-m-d')])
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

        $this->layout = '/print';

        return $this->render('/report/royalty/_print', [
                'searchModel' => $searchModel,
                'invoiceTaxTotal' => $invoiceTaxTotal,
                'payments' => $payments,
                'royaltyFreeAmount' => $royaltyFreeAmount,
				'giftCardPayments' => $giftCardPayments,
        ]);
    }
    
    public function actionUser()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination=false;
        $this->layout = '/print';

        return $this->render('/user/_print', [
                'searchModel'=>$searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCustomerItemsPrint()
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

        $this->layout             = '/print';

        return $this->render('/report/customer-item/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    public function actionProformaInvoice($id)
    {
        $model = ProformaInvoice::findOne($id);
        $lessonLineItems = Lesson::find()
            ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonLineItems,
            'pagination' => false,
        ]);
	$invoiceLineItems = Invoice::find()
            ->joinWith(['proformaInvoiceItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);
	$searchModel = new ProformaInvoiceSearch();
	$searchModel->showCheckBox = false;
        $searchModel->isPrint = true;
        $this->layout = '/print';

        return $this->render('/proforma-invoice/print/view', [
            'model' => $model,
	        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'searchModel'=>$searchModel,
        ]);
    }
    
    public function actionPayment($id) 
    {
        $model = Payment::findOne($id);
        $lessonPayment = Lesson::find()
            ->privateLessons()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id])
			->notDeleted();
            }]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
            'pagination' => false
        ]);
        
        $groupLessonPayment = GroupLesson::find()
            ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id])
            ->notDeleted();
            }]);

        $groupLessonDataProvider = new ActiveDataProvider([
            'query' => $groupLessonPayment,
            'pagination' => false
        ]);

        $invoicePayment = Invoice::find()
            ->notDeleted()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->andWhere(['payment_id' => $id])
			->notDeleted();
            }]);
	    
	    $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoicePayment,
            'pagination' => false
        ]);
        $this->layout = '/print';
	
        return $this->render('/payment/print/view', [
            'model' => $model,
	        'lessonDataProvider' => $lessonDataProvider,
            'invoiceDataProvider' => $invoiceDataProvider,
            'groupLessonDataProvider' =>  $groupLessonDataProvider
        ]);
    }

    public function actionReceipt()
    {
        $model = Yii::$app->session->get('model');
        if ($model) {
            $customer =  User::findOne(['id' => $model->userId]);
            $searchModel  =  new ProformaInvoiceSearch();
            $searchModel->showCheckBox = false;
            $paymentsLineItemsDataProvider = $model->getUsedCredit();
            $invoiceLineItemsDataProvider = $model->getInvoicesPaid();
            $lessonLineItemsDataProvider = $model->getLessonsPaid();
            $groupLessonLineItemsDataProvider = $model->getGroupLessonsPaid();
            $paymentNew = Payment::findOne(['id' => $model->paymentId]);
            
            $this->layout = '/print';
            return $this->render('/receive-payment/print/view', [
                'model'                        => !empty($model) ? $model : new Payment(),
                'lessonLineItemsDataProvider' =>  $lessonLineItemsDataProvider,
                'invoiceLineItemsDataProvider' =>  $invoiceLineItemsDataProvider,
                'groupLessonLineItemsDataProvider' =>  $groupLessonLineItemsDataProvider,
                'paymentsLineItemsDataProvider'  =>  $paymentsLineItemsDataProvider,
                'searchModel'                  =>  $searchModel,
                'customer'                     =>   $customer,
                'payment'                      => $paymentNew,
            ]);
        }
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
        
        $this->layout = '/print';
        return $this->render('/report/sales-and-payment/_print', [
            'searchModel' => $searchModel,
            'salesDataProvider' => $salesDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider,
        ]);
    }

    public function actionCustomerStatement($id)
    {
        $groupLessonSearchModel = new PaymentFormGroupLessonSearch();
        $groupLessonSearchModel->showCheckBox = true;
        $groupLessonSearchModel->userId = $id;
        $currentDate = new \DateTime();
        $searchModel = new PaymentFormLessonSearch();
        $searchModel->showCheckBox = true;
        $searchModel->userId = $id;
        $groupLessonSearchModel->fromDate = $currentDate->format('M 1, Y');
        $groupLessonSearchModel->toDate = $currentDate->format('M t, Y'); 
        $groupLessonSearchModel->dateRange = $groupLessonSearchModel->fromDate . ' - ' . $groupLessonSearchModel->toDate;
        $searchModel->fromDate = $currentDate->format('M 1, Y');
        $searchModel->toDate = $currentDate->format('M t, Y'); 
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $groupLessonsQuery = $groupLessonSearchModel->search(Yii::$app->request->queryParams);
        $groupLessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
        $lessonsQuery = $searchModel->search(Yii::$app->request->queryParams);
        $lessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonsQuery,
            'pagination' => false
        ]);
        $groupLessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $groupLessonsQuery,
            'pagination' => false
        ]);
        $invoicesQuery = Invoice::find();
        if (!$searchModel->userId) {
            $searchModel->userId = null;
        }
        $invoicesQuery->notDeleted()
            ->invoice()
            ->customer($searchModel->userId)
            ->unpaid()
            ->andWhere(['>','invoice.balance' , 0.09]);
        $invoicesQuery->orderBy(['invoice.id' => SORT_ASC]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoicesQuery,
            'pagination' => false 
        ]);
        $model = new Payment();
        $creditDataProvider = $model->getAvailableCredit($searchModel->userId);
        $user = User::findOne($id);
        
        $lessonsDue = $lessonsQuery->sum('private_lesson.balance');
        $invoicesDue = $invoicesQuery->sum('invoice.balance');
        $groupLessonsDue = $groupLessonsQuery->sum('group_lesson.balance');
        $credits = 0.00;
        $creditResults = $creditDataProvider->getModels();   
        foreach ($creditResults as $creditResult) {
            $credits+= $creditResult['amount'];
        }    
        $total = ($lessonsDue+$invoicesDue+$groupLessonsDue) - $credits;
        $this->layout = '/print';
        $customerStatement = new CustomerStatement();
        $customerStatement->userId = $id;
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $customerStatement->on(CustomerStatement::EVENT_PRINT, [new CustomerStatementLog(), 'customerStatement'], ['loggedUser' => $loggedUser, 'activity' => LogActivity::TYPE_PRINT]);
        $customerStatement->trigger(CustomerStatement::EVENT_PRINT);
        return $this->render('/receive-payment/customer-statement/print-view', [
        'user' => $user,
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
        'creditDataProvider' => $creditDataProvider,
        'searchModel' => $searchModel,
        'groupLessonSearchModel' => $groupLessonSearchModel,
        'total' => $total
        ]);
    }

    public function actionAllLocations()
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
        $this->layout = '/print';
        return $this->render('/report/all-locations/_print', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccountsReceivable()
    {
        $searchModel = new ReportSearch();
        $request = Yii::$app->request;
        $searchModel->load($request->get());
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $customersInclude = [];
        $customers = User::find()
                ->customers($locationId)
                ->notDeleted()
                ->all();
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
            'pagination' => false,
        ]);
        $this->layout = '/print';
        return $this->render('/report/account-receivable/_print', [
                'dataProvider' => $dataProvider,
        ]);
    }
    public function actionGroupEnrolment($id)
    {
        $model = Enrolment::findOne($id);
        $scheduleHistoryDataProvider = new ActiveDataProvider([
            'query' => CourseSchedule::find()
            ->andWhere(['courseId' => $model->courseId]),
        ]);
	    $lessonCount = Lesson::find()
			->andWhere(['courseId' => $model->course->id])
            ->notDeleted()
            ->scheduledOrRescheduled()
            ->notCompleted()
            ->count();
        $query = Lesson::find()
        ->andWhere(['lesson.courseId' => $model->course->id])
        ->scheduledOrRescheduled()
        ->isConfirmed()
        ->notDeleted()
        ->notCompleted();
        if ($model->course->isPrivate()) {
            $query->orderBy([
                'lesson.dueDate' => SORT_ASC,
                'lesson.date' => SORT_ASC      
                ]);
        } else {
            $query->joinWith(['groupLesson' => function ($query) use ($id) {
                $query->enrolment($id)
                ->notDeleted();
            }])
                ->orderBy([
                'group_lesson.dueDate' => SORT_ASC,
                'lesson.date' => SORT_ASC      
                ]);
        }
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        $this->layout = '/print';
        return $this->render('/enrolment/_print', [
            'model' => $model,
            'lessonDataProvider' => $lessonDataProvider,
            'scheduleHistoryDataProvider' => $scheduleHistoryDataProvider,
        ]);
    }
}
