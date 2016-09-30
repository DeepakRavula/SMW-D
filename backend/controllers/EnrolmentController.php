<?php

namespace backend\controllers;

use Yii;
use common\models\Enrolment;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use backend\models\search\EnrolmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class EnrolmentController extends Controller
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
     * Lists all Enrolment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EnrolmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Enrolment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);        
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->where(['courseId' => $model->course->id]),                
        ]);        
       
        return $this->render('view', [ 
                'model' => $model,                
                'lessonDataProvider' => $lessonDataProvider,
            ]); 
    }

    /**
     * Creates a new Enrolment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Enrolment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Enrolment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$courseModel = $model->course;
        if ($courseModel->load(Yii::$app->request->post())) {
			$courseDate = \DateTime::createFromFormat('d-m-Y',$courseModel->rescheduleBeginingDate);
			$courseDate = $courseDate->format('Y-m-d H:i:s');
			$lessons = Lesson::find()
				->where(['courseId' => $courseModel->id])
				->andWhere(['>=', 'date', $courseDate])
				->all();
			foreach($lessons as $lesson){
				$changedFromTime = new \DateTime($courseModel->fromTime);
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lesson->date);
				$lessonTime = $lessonDate->format('H:i:s');
                $lessonStartTime = new \DateTime($lessonTime);
                $duration = $changedFromTime->diff($lessonStartTime);
				if($changedFromTime > $lessonStartTime){
					$lessonDate->add(new \DateInterval('PT' . $duration->h. 'H' . $duration->i . 'M'));	
				} else {
					$lessonDate->sub(new \DateInterval('PT' . $duration->h. 'H' . $duration->i . 'M'));	
				}
            	$length = explode(':', $model->course->duration);
            	$changedFromTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
				$lesson->updateAttributes([
					'date' => $lessonDate->format('Y-m-d H:i:s'),
					'toTime' => $changedFromTime->format('H:i:s'),
					'status' => Lesson::STATUS_DRAFTED,
				]);
			}
            return $this->redirect(['/lesson/review', 'courseId' => $model->course->id]);
        } else {
            return $this->render('update', [
                'model' => $model->course,
            ]);
        }
    }

    /**
     * Deletes an existing Enrolment model.
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
     * Finds the Enrolment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Enrolment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Enrolment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionSendMail($id) {
		$model = $this->findModel($id);        
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->where(['courseId' => $model->course->id]), 
            'pagination' => [
                'pageSize' => 60,
             ],
        ]);
		$subject = 'Schedule for ' . $model->student->fullName;
		if(! empty($model->student->customer->email)){
			Yii::$app->mailer->compose('lesson-schedule', [
				'model' => $model,
				'toName' => $model->student->customer->publicidentity,
				'lessonDataProvider' => $lessonDataProvider,
			])
				->setFrom(\Yii::$app->params['robotEmail'])
				->setTo($model->student->customer->email)
				->setSubject($subject)
				->send();

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => ' Mail has been send successfully'
			]);
		}else{
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-danger'],
				'body' => 'The customer doesn\'t have email id' 
			]);	
		}
		return $this->redirect(['view', 'id' => $model->id]);
	} 
}
