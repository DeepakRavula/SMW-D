<?php

namespace backend\controllers;

use Yii;
use common\models\GroupCourse;
use common\models\GroupEnrolment;
use common\models\Student;
use common\models\User;
use common\models\InvoiceLineItem;
use common\models\Invoice;
use common\models\ItemType;
use common\models\TaxStatus;
use backend\models\search\GroupCourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
/**
 * GroupCourseController implements the CRUD actions for GroupCourse model.
 */
class GroupCourseController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all GroupCourse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupCourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupCourse model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$request = Yii::$app->request;
		$groupEnrolment = $request->post('GroupEnrolment');
		$studentIds = $groupEnrolment['studentIds']; 
		if( ! empty($studentIds)){	
			GroupEnrolment::deleteAll(['course_id' => $id]);
			foreach($studentIds as $studentId){
				$groupEnrolment = new GroupEnrolment();
				$groupEnrolment->setAttributes([
					'course_id'	 => $id,
					'student_id' => $studentId,
				]);
				$groupEnrolment->save();
			} 
		}

		$studentDataProvider = new ActiveDataProvider([
			'query' => Student::find()
				->enrolled($id),
		]);
	 
        return $this->render('view', [
            'model' => $this->findModel($id),
			'studentDataProvider' => $studentDataProvider,
        ]);
    }

    /**
     * Creates a new GroupCourse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GroupCourse();
		$teacherModel = ArrayHelper::map(User::find()
					->joinWith('userLocation ul')
					->join('INNER JOIN','rbac_auth_assignment raa','raa.user_id = user.id')
					->where(['raa.item_name' => 'teacher'])
					->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
					->all(),
				'id','userProfile.fullName'		
			);
		$model->location_id = Yii::$app->session->get('location_id');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Updates an existing GroupCourse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$teacherModel = ArrayHelper::map(User::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN','rbac_auth_assignment raa','raa.user_id = user.id')
			->where(['raa.item_name' => 'teacher'])
			->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
			->all(),
			'id','userProfile.fullName'		
		);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Deletes an existing GroupCourse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the GroupCourse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GroupCourse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = GroupCourse::find()
			->where(['id' => $id,'location_id' => $locationId])->one();
				if ($model !== null) {
					return $model;
				} else {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
    }

	public function actionInvoice($id, $studentId) {
		$model = GroupCourse::findOne(['id' => $id]);
		$studentModel = Student::findOne(['id' => $studentId]);
		$currentDate = new \DateTime();
		$location_id = Yii::$app->session->get('location_id');
		$lastInvoice = Invoice::lastInvoice($location_id);
		if(empty($lastInvoice)) {
			$invoiceNumber = 1;
		} else {
			$invoiceNumber = $lastInvoice->invoice_number + 1;
		}

		$invoice = new Invoice();
		$invoice->user_id = $studentModel->customer->id; 
		$invoice->location_id = $location_id;
		$invoice->invoice_number = $invoiceNumber;
		$invoice->date = (new \DateTime())->format('Y-m-d');
		$invoice->status = Invoice::STATUS_OWING;
		$invoice->type = INVOICE::TYPE_INVOICE;
		$invoice->save();
		$subTotal = 0;
		$taxAmount = 0;
		$invoiceLineItem = new InvoiceLineItem();
		$invoiceLineItem->invoice_id = $invoice->id;
		$invoiceLineItem->item_id = $model->id;
		$invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
		$taxStatus = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
		$invoiceLineItem->tax_type = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
		$invoiceLineItem->tax_rate = 0.0;
		$invoiceLineItem->tax_code = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
		$invoiceLineItem->tax_status = $taxStatus->name;
		$description = $model->program->name . ' for ' . $studentModel->fullName . ' with ' . $studentModel->groupCourse->teacher->publicIdentity;
		$invoiceLineItem->description = $description;
		$time = explode(':', $model->length);
		$invoiceLineItem->unit = (($time[0] * 60) + ($time[1])) / 60;
		$invoiceLineItem->amount = $model->program->rate * $invoiceLineItem->unit;
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
}
