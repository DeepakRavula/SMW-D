<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\User;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
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
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Invoice::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();
		$request = Yii::$app->request;
		$invoice = $request->get('Invoice');
		$unInvoicedLessonsDataProvider = null;

		if(isset($invoice['customer_id'])) {
			$customer = User::findOne(['id' => $invoice['customer_id']]);

			if(empty($customer)) {
            	throw new NotFoundHttpException('The requested page does not exist.');
			}

			$model->customer_id = $customer->id;
			$location_id = Yii::$app->session->get('location_id');
       		$query = Lesson::find()
                ->joinwith('invoiceLineItem ili')
                ->joinwith(['enrolmentScheduleDay' => function($query) use($location_id, $customer) {
					$query->joinWith(['enrolment e' => function($query) use($customer) {
						$query->joinWith('student s')
								->where(['s.customer_id' => $customer->id]);
					}])
					->where(['e.location_id' => $location_id]);
				}])
                ->where([
					'ili.id' => null,
				]);
        
			$unInvoicedLessonsDataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
		}

		$post = $request->post();
        if ( ! empty($post['selection']) && is_array($post['selection'])) {
			$invoice = new Invoice();
			$invoice->invoice_number = 1;
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->status = Invoice::STATUS_OWING;
			$invoice->save();
			print_r($invoice->getErrors());die;
			foreach($post['selection'] as $lesson) {
			}
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
            ]);
        }
    }

    /**
     * Updates an existing Invoice model.
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
     * Deletes an existing Invoice model.
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
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionAddInvoice()
    {
        $model = new User();
        
        if( isset(Yii::$app->request->queryParams['User']))
        {
            $model->customer = Yii::$app->request->queryParams['User']["customer"];
        }        

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    public function generateInvoice()
    {
        $query = Student::find()
					//->join('INNER JOIN','user_location','user_location.user_id = Student.id')
					->join('INNER JOIN','enrolment','enrolment.student_id = Student.id')
                    ->join('INNER JOIN','enrolment_schedule_day','enrolment_schedule_day.enrolment_id = enrolment.id')
                    ->join('INNER JOIN','qualification','qualification.id = enrolment.qualification_id')
                    ->join('INNER JOIN','program','program.id = qualification.program_id')
					->where(['enrolment.location_id' => Yii::$app->session->get('location_id')])	
                ->select('first_name, program.name, enrolment_schedule_day.duration')
				->all();
        //$query =  Student::find()->with('enrolment', 'enrolment_schedule_day')->all();    
        $query = enrolment::find()
                ->joinwith('student s')
                ->joinwith('enrolmentScheduleDay es')
                ->joinwith('qualification q')
                ->joinWith('qualification.program p')
                ->groupBy('p.name')
                ->where(['location_id' => Yii::$app->session->get('location_id'), 's.customer_id' => 171]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->renderPartial('_invoice', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
