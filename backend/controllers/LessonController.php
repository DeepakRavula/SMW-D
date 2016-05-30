<?php

namespace backend\controllers;

use Yii;
use common\models\Enrolment;
use common\models\EnrolmentScheduleDay;
use common\models\Lesson;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends Controller
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
        ];
    }

    /**
     * Lists all Lesson models.
     * @return mixed
     */
    public function actionIndex()
    {
		$session = Yii::$app->session;
        $dataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
					->join('INNER JOIN','enrolment_schedule_day','enrolment_schedule_day.id = enrolment_schedule_day_id')
					->join('INNER JOIN','enrolment','enrolment.id = enrolment_schedule_day.enrolment_id')
					->where(['enrolment.location_id' => $session->get('location_id') ])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lesson model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Lesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lesson();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Lesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Lesson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lesson the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lesson::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionInvoice($id) {
		$invoice = new Invoice();
		$model = Lesson::findOne(['id' => $id]);
		$invoice->invoice_number = 1;
		$invoice->date = (new \DateTime())->format('Y-m-d');
		$invoice->status = Invoice::STATUS_OWING;
		$invoice->save();
        $subTotal=0;

		$invoiceLineItem = new InvoiceLineItem();
		$invoiceLineItem->invoice_id = $invoice->id;
		$invoiceLineItem->lesson_id = $id;
		$time = explode(':', $model->enrolmentScheduleDay->duration);
		$invoiceLineItem->unit = (($time[0] * 60) + ($time[1])) / 60;
		$invoiceLineItem->amount = $model->enrolmentScheduleDay->enrolment->qualification->program->rate;
		$invoiceLineItem->save();

		$subTotal += $invoiceLineItem->amount;

		$invoice = Invoice::findOne(['id' => $invoice->id]);
		$invoice->subTotal = $subTotal;
		$taxPercentage = $model->enrolmentScheduleDay->enrolment->location->province->tax_rate;
		$taxAmount = $subTotal * $taxPercentage / 100;
		$totalAmount = $subTotal + $taxAmount;
		$invoice->tax = $taxAmount;
		$invoice->total = $totalAmount;
		$invoice->save();

		return $this->redirect(['lesson/index','id' => $id]);
	}
}
