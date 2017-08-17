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
}
