<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use common\models\Payment;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use common\models\Course;
use common\models\Lesson;
use common\models\ExamResult;
use common\models\Student;
use common\models\User;
use backend\models\search\LessonSearch;
use backend\models\search\InvoiceSearch;
use backend\models\search\UserSearch;
use common\models\CustomerAccount;
use common\models\CompanyAccount;
use backend\models\search\ReportSearch;
use common\models\PaymentMethod;
use backend\models\search\InvoiceLineItemSearch;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

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
                        'actions' => ['invoice', 'course', 'evaluation', 'teacher-lessons', 'time-voucher', 'customer-invoice', 'account-view', 'royalty', 'royalty-free', 'tax-collected', 'user', 'customer-items-print'],
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
                ->where(['invoice_id' => $id]);
        $payments = Payment::find()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->where(['invoice_id' => $id]);
            }])
             ->groupBy(['payment.id','payment.payment_method_id']);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
            'pagination' => false,
        ]);
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments,
        ]);
        $this->layout = '/print';

        return $this->render('/invoice/print/view', [
            'model' => $model,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider,
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
        $examResults = ExamResult::find()->where(['studentId' => $studentId]);
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
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
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
        $invoiceSearch = $request->get('InvoiceSearch');
        
        if (!empty($invoiceSearch)) {
            $invoiceSearchModel->dateRange = $invoiceSearch['dateRange'];
            list($invoiceSearchModel->fromDate, $invoiceSearchModel->toDate) = explode(' - ', $invoiceSearchModel->dateRange);
            $invoiceSearchModel->summariseReport = $invoiceSearch['summariseReport'];
        }
        $timeVoucher = InvoiceLineItem::find()
                        ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($invoiceSearchModel) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between((new\DateTime($invoiceSearchModel->fromDate))->format('Y-m-d'), (new\DateTime($invoiceSearchModel->toDate))->format('Y-m-d'));
            }])
            ->joinWith(['lesson' => function ($query) use ($model) {
                $query->andWhere(['lesson.teacherId' => $model->id]);
            }]);
        if ($invoiceSearchModel->summariseReport) {
            $timeVoucher->groupBy(['invoice.id','DATE(invoice.date)']);
        } else {
            $timeVoucher->orderBy(['invoice.date' => SORT_ASC]);
        }
            
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
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
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
                ->where([
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
                    ->where(['userId' => $id])
                    ->orderBy(['transactionId' => SORT_ASC]);
        } else {
            $accountQuery = CustomerAccount::find()
                    ->where(['userId' => $id])
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $royaltyFreeItems = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $searchModel) {
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $invoiceTaxes = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $searchModel) {
                $query->andWhere([
                        'location_id' => $locationId,
                        'type' => Invoice::TYPE_INVOICE,
                    ])
                    ->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->notDeleted();
            }])
            ->andWhere(['>', 'tax_rate', 0]);
        if ($searchModel->summarizeResults) {
            $invoiceTaxes ->groupBy(['invoice.id','DATE(invoice.date)']);
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;

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
                ->notDeleted()
            ->joinWith(['invoice i' => function ($query) use ($locationId) {
                $query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
            }])
            ->andWhere(['between', 'i.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
            ->royaltyFree()
            ->sum('invoice_line_item.amount');

        $this->layout = '/print';

        return $this->render('/report/royalty/_print', [
                'searchModel' => $searchModel,
                'invoiceTaxTotal' => $invoiceTaxTotal,
                'payments' => $payments,
                'royaltyPayment' => $royaltyPayment,
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
}
