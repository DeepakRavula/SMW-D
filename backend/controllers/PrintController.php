<?php

namespace backend\controllers;

use Yii;
use common\models\InvoiceLineItem;
use common\models\Payment;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use common\models\Course;
use common\models\Lesson;
use common\models\ExamResult;
use common\models\Student;
use common\models\User;
use backend\models\search\LessonSearch;
use backend\models\search\InvoiceSearch;
use backend\models\search\UserSearch;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class PrintController extends Controller
{
	public function actionInvoice($id)
    {
        $model = Invoice::findOne(['id' => $id]);
        $invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
        $payments = Payment::find()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->where(['invoice_id' => $id]);
            }])
            ->groupBy('payment.payment_method_id');
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
				->andWhere([
					'courseId' => $model->id,
					'status' => Lesson::STATUS_SCHEDULED
				])
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
        $locationId = $session->get('location_id');
		$request = Yii::$app->request;
		$lessonSearch = new LessonSearch();
		$lessonSearch->fromDate = new \DateTime();
		$lessonSearch->toDate = new \DateTime();
		$lessonSearchModel = $request->get('LessonSearch');
		
		if(!empty($lessonSearchModel)) {
			$lessonSearch->fromDate = new \DateTime($lessonSearchModel['fromDate']);
			$lessonSearch->toDate = new \DateTime($lessonSearchModel['toDate']);
		}
		$teacherLessons = Lesson::find()
			->innerJoinWith('enrolment')
			->location($locationId)
			->where(['lesson.teacherId' => $model->id])
			->isConfirmed()
			->notDeleted()
			->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED, Lesson::STATUS_SCHEDULED]])
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
		$invoiceSearch = new InvoiceSearch();
		$invoiceSearch->fromDate = new \DateTime();
		$invoiceSearch->toDate = new \DateTime();
		$invoiceSearchModel = $request->get('InvoiceSearch');
		
		if(!empty($invoiceSearchModel)) {
			$invoiceSearch->fromDate = new \DateTime($invoiceSearchModel['fromDate']);
			$invoiceSearch->toDate = new \DateTime($invoiceSearchModel['toDate']);
			$invoiceSearch->summariseReport = $invoiceSearchModel['summariseReport']; 
		}
		$timeVoucher = InvoiceLineItem::find()
			->joinWith(['invoice' => function($query) use($invoiceSearch) {
				$query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
					->between($invoiceSearch->fromDate->format('Y-m-d'), $invoiceSearch->toDate->format('Y-m-d'));
			}])
			->joinWith(['lesson' => function($query) use($model){
				$query->andWhere(['lesson.teacherId' => $model->id]);
			}]);
			if($invoiceSearch->summariseReport) {
				$timeVoucher->groupBy('DATE(invoice.date)');	
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
			'fromDate' => $invoiceSearch->fromDate,
			'toDate' => $invoiceSearch->toDate,
			'searchModel' => $invoiceSearch
        ]);
    }
		public function actionCustomerInvoice($id)
    {
        $model = User::findOne(['id' => $id]);
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
		$request = Yii::$app->request;
        $currentDate = new \DateTime();
        $model->fromDate = $currentDate->format('1-m-Y');
        $model->toDate = $currentDate->format('t-m-Y');
        $model->dateRange = $model->fromDate . ' - ' . $model->toDate;
        $userRequest = $request->get('User');
		if(!empty($userRequest)) {
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
				->between($fromDate,$toDate);
		if(!empty($invoiceStatus) && (int)$invoiceStatus !== UserSearch::STATUS_ALL) {
			$invoiceQuery->andWhere(['invoice.status' => $invoiceStatus]);
		}
		if(!empty($studentId)) {
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
}