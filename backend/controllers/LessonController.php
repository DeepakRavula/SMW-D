<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\PrivateLesson;
use common\models\Enrolment;
use common\models\Program;
use common\models\Course;
use common\models\Student;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use common\models\ItemType;
use common\models\TaxStatus;
use yii\data\ActiveDataProvider;
use backend\models\search\LessonSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;

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
		$searchModel->lessonStatus = Lesson::STATUS_COMPLETED;
		$request = Yii::$app->request;
		$invoiceRequest = $request->get('LessonSearch');
		$searchModel->type = $invoiceRequest['type'];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lesson model.
     * @param string $id
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
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			if(empty($model->date)){
				$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
				$model->date = $lessonDate->format('Y-m-d H:i:s');            
			}
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
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$model->delete();

        return $this->redirect(['review', 'courseId' => $model->courseId]);
    }

    /**
     * Finds the Lesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
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

	public function actionMissed($id)
    {
        $model = $this->findModel($id);

		if( ! empty($model->privateLesson->id)) {
            $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
        } else {
		$privateLessonModel = new PrivateLesson();
		}
		
		if ($privateLessonModel->load(Yii::$app->request->post()) ) {
			$privateLessonModel->lessonId = $model->id;
			if(! empty($privateLessonModel->expiryDate)){
				$expiryDate = \DateTime::createFromFormat('d-m-Y g:i A', $privateLessonModel->expiryDate);
				$privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
			} else {
				$privateLessonModel->expiryDate = $model->date;	
            	return $this->redirect(['view', 'id' => $model->id]);   
			}
			$privateLessonModel->save();
		}
		if($model->load(Yii::$app->request->post())){	
			if(empty($model->date)){
				$model->date = $model->getOldAttribute('date');
				$model->status = Lesson::STATUS_CANCELED;
			} else {
			$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
			$model->date = $lessonDate->format('Y-m-d H:i:s');
			}
			$model->save();
					
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Lesson has been marked as missed successfully'
            ]);
            return $this->redirect(['view', 'id' => $model->id]);   
		}
		
        return $this->render('_form-private-lesson', [
            'model' => $model,
			'privateLessonModel' => $privateLessonModel, 
        ]);
	}
	
    public function actionUpdateField($id){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->post('hasEditable')) {
            $lessonIndex = Yii::$app->request->post('editableIndex');
            $model = Lesson::findOne(['id' => $id]);
            $result = [
				'output' => '',
				'message' => ''
			];
            $post = Yii::$app->request->post();
            if( ! empty($post['Lesson'][$lessonIndex]['date'])){
                $existingDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
                $lessonTime = $existingDate->format('H:i:s');
                $timebits = explode(":", $lessonTime);
                $changedDate = new \DateTime($post['Lesson'][$lessonIndex]['date']);
                $changedDate->add(new \DateInterval('PT' . $timebits[0]. 'H' . $timebits[1] . 'M'));
                $model->date = $changedDate->format('Y-m-d H:i:s');
                $output = Yii::$app->formatter->asDate($model->date);
            }
            if(! empty($post['Lesson'][$lessonIndex]['time'])){
                $existingDate = (new \DateTime($model->date))->format('Y-m-d');
                $existingDate = new \DateTime($existingDate);
                $changedTime = new \DateTime($post['Lesson'][$lessonIndex]['time']);
                $lessonTime = $changedTime->format('H:i:s');
                $timebits = explode(":", $lessonTime);
                $existingDate->add(new \DateInterval('PT' . $timebits[0]. 'H' . $timebits[1] . 'M'));
                $model->date = $existingDate->format('Y-m-d H:i:s');
                $newTime = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
                $output = Yii::$app->formatter->asTime($newTime);
            }
			if(! empty($post['Lesson'][$lessonIndex]['toTime'])){
               	$toTime = \DateTime::createFromFormat('h:i A', $post['Lesson'][$lessonIndex]['toTime']);
                $model->toTime = $toTime->format('H:i:s');
                $output = Yii::$app->formatter->asTime($model->toTime);
            }

            $model->save();
            $result = [
				'output' => $output,
				'message' => ''
			];
            return $result;
        }	
    }

	public function actionReview($courseId){	
		$request = Yii::$app->request;
		$enrolmentRequest = $request->get('Enrolment'); 
		$rescheduleBeginDate = $enrolmentRequest['rescheduleBeginDate'];
		$courseModel = Course::findOne(['id' => $courseId]);
		$draftLessons = Lesson::find()
			->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
			->all();
		foreach($draftLessons as $draftLesson){
			$draftLesson->setScenario('review');
		}
		Model::validateMultiple($draftLessons);
		$conflicts = [];
		foreach($draftLessons as $draftLesson){
			$conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
		}
		$lessonDataProvider = new ActiveDataProvider([
		    'query' => Lesson::find()->indexBy('id')
				->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
		]);	
		
		$model = new Lesson();
		$post = Yii::$app->request->post();
		if($model->load($post)){
			$model->courseId = $courseId;
			$lessonDate = \DateTime::createFromFormat('d-m-Y g:i A',$model->date);
			$model->date = $lessonDate->format('Y-m-d H:i:s');
			$model->status = Lesson::STATUS_DRAFTED;
			$model->isDeleted = 0;
            $fromTime = $lessonDate->format('H:i:s');
            $fromTime = new \DateTime($fromTime);
            $length = explode(':', $model->course->duration);
            $fromTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
            $toTime = $fromTime->format('H:i:s');
			$model->save();
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Lesson has been created successfully'
			]);
		}
		return $this->render('_review', [				
			'courseModel' => $courseModel,
			'courseId' => $courseId,
			'lessonDataProvider' => $lessonDataProvider,
			'conflicts' => $conflicts,
			'rescheduleBeginDate' => $rescheduleBeginDate,
        ]);	
	}

	public function actionConfirm($courseId){        
        $courseModel = Course::findOne(['id' => $courseId]);
		$lessons = Lesson::findAll(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
		$request = Yii::$app->request;
        $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]); 
        $enrolmentModel->isConfirmed = true;
        $enrolmentModel->save();
        $enrolmentRequest = $request->get('Enrolment');
		$rescheduleBeginDate = $enrolmentRequest['rescheduleBeginDate'];
		if( ! empty($rescheduleBeginDate)) {
			$courseDate = \DateTime::createFromFormat('d-m-Y',$rescheduleBeginDate);
			$courseDate = $courseDate->format('Y-m-d H:i:s');
			$oldLessons = Lesson::find()
				->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_SCHEDULED])
				->andWhere(['>=', 'date', $courseDate])
				->all();
			$oldLessonIds = [];
			foreach($oldLessons as $oldLesson){
				$oldLessonIds[] = $oldLesson->id;
				$oldLesson->delete();
			}	
			foreach($lessons as $i => $lesson){
				$lesson->updateAttributes([
					'id' => $oldLessonIds[$i],
					'status' => Lesson::STATUS_SCHEDULED,
				]);
			}
		} else {
			foreach($lessons as $lesson){
				$lesson->updateAttributes([
					'status' => Lesson::STATUS_SCHEDULED,
				]);
			}
		}
		
		Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Lessons have been created successfully'
		]);
        if((int) $courseModel->program->type === (int) Program::TYPE_PRIVATE_PROGRAM) { 
            return $this->redirect(['student/view', 'id' => $courseModel->enrolment->student->id, '#' => 'lesson']);
        }
        else {
            return $this->redirect(['course/view', 'id' => $courseId]);   
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
			$lessonStartTime = $lessonDate->format('H:i:s');
			$lessonStartTime = new \DateTime($lessonStartTime);
			$lessonEndTime = new \DateTime($model->toTime);
			$duration = $lessonStartTime->diff($lessonEndTime);
            $invoiceLineItem->unit = (($duration->h * 60) + ($duration->i)) / 60;
			if((int) $model->course->program->type === (int) Program::TYPE_GROUP_PROGRAM){
	        	$invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
				$courseFee = $model->course->program->rate;
				$courseCount = Lesson::find()
					->where(['courseId' => $model->courseId])
					->count('id');
				$lessonAmount = $model->course->program->rate / $courseCount;
            	$invoiceLineItem->amount = $lessonAmount;
			} else {
	        	$invoiceLineItem->item_type_id = ItemType::TYPE_PRIVATE_LESSON;
            	$invoiceLineItem->amount = $model->course->program->rate * $invoiceLineItem->unit;
			}
			$taxStatus = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
			$invoiceLineItem->tax_type = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
			$invoiceLineItem->tax_rate = 0.0;
			$invoiceLineItem->tax_code = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
			$invoiceLineItem->tax_status = $taxStatus->name;
			$description = $model->enrolment->program->name . ' for ' . $model->enrolment->student->fullName . ' with ' . $model->teacher->publicIdentity;
            $invoiceLineItem->description = $description;
			$invoiceLineItem->isRoyalty = true;	
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
