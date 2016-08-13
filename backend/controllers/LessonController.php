<?php

namespace backend\controllers;

use Yii;
use backend\models\search\LessonSearch;
use common\models\Lesson;
use common\models\Invoice;
use common\models\ItemType;
use common\models\User;
use common\models\InvoiceLineItem;
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
        $searchModel = new LessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
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
		
        if ($model->load(Yii::$app->request->post())) {
			$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
   	   		$model->date = $lessonDate->format('Y-m-d H:i:s');
			$model->save();
		
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
		Yii::$app->session->setFlash('alert', [
								'options' => ['class' => 'alert-success'],
								'body' => 'lesson has been deleted successfully'
						]);	
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
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = Lesson::find()->location($locationId)
			->where(['lesson.id' => $id])->one();
				if ($model !== null) {
					return $model;
				} else {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
    }

	public function actionInvoice($id) {
		$model = Lesson::findOne(['id' => $id]);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		$currentDate = new \DateTime();
		$location_id = Yii::$app->session->get('location_id');
		$lastInvoice = Invoice::lastInvoice($location_id);
		if(empty($lastInvoice)) {
			$invoiceNumber = 1;
		} else {
			$invoiceNumber = $lastInvoice->invoice_number + 1;
		}

		if($lessonDate <= $currentDate){
			$invoice = new Invoice();
			$invoice->user_id = $model->enrolment->student->customer->id; 
			$invoice->invoice_number = $invoiceNumber;
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->type = INVOICE::TYPE_INVOICE;
			$invoice->save();
       		$subTotal = 0;
			$taxAmount = 0;
            $invoiceLineItem = new InvoiceLineItem();
            $invoiceLineItem->invoice_id = $invoice->id;
            $invoiceLineItem->item_id = $model->id;
            $invoiceLineItem->item_type_id = ItemType::TYPE_LESSON;
			$description = $model->enrolment->program->name . ' for ' . $model->enrolment->student->fullName . ' with ' . $model->teacher->publicIdentity;
            $invoiceLineItem->description = $description;
            $time = explode(':', $model->enrolment->duration);
            $invoiceLineItem->unit = (($time[0] * 60) + ($time[1])) / 60;
            $invoiceLineItem->amount = $model->enrolment->program->rate * $invoiceLineItem->unit;
            $invoiceLineItem->save();
            $subTotal += $invoiceLineItem->amount;                
            $invoice = Invoice::findOne(['id' => $invoice->id]);
            $invoice->subTotal = $subTotal;
            $totalAmount = $subTotal + $taxAmount;
            $invoice->tax = $taxAmount;
            $invoice->total = $totalAmount;
            $invoice->save();
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Invoice has been generated successfully'
            ]); 
            
            return $this->redirect(['invoice/view','id' => $invoice->id]);
	
		}
        else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Generate invoice against completed lesson only.'
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }
}
