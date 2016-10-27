<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\TeacherAvailability;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Course;
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
                ->where(['courseId' => $model->course->id])
				->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
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
        $request        = Yii::$app->request;
        $post           = $request->post();
        $model          = $this->findModel($id);
        $teacher        = User::findOne(['id' => $model->course->teacherId]);
        $teacherDetails = $teacher->teacherAvailabilityWithLessons($model->course->teacherId);
        $lessons        = Lesson::find()
            ->where(['courseId' => $model->course->id])
            ->andWhere(['status' => Lesson::STATUS_SCHEDULED])
            ->all();
        $lastLessonDate = new \DateTime(end($lessons)->date);
        $getDuration = \DateTime::createFromFormat('H:i:s', $model->course->duration);
        $hours = $getDuration->format('H');
        $minutes = $getDuration->format('i');
        $durationMinutes = ($hours * 60) + $minutes;
        if ($model->course->load(Yii::$app->request->post())) {
            $dayList               = TeacherAvailability::getWeekdaysList();
            (int) $model->course->day    = array_search($model->course->day,
                $dayList);
            $lessonFromDate        = \DateTime::createFromFormat('d-m-Y',
                    $model->course->lessonFromDate);
            $lessonToDate          = \DateTime::createFromFormat('d-m-Y',
                    $model->course->lessonToDate);
            Lesson::deleteAll([
                'courseId' => $model->course->id,
                'status' => Lesson::STATUS_DRAFTED,
            ]);
            $rescheduleLessons     = Lesson::find()
                ->where(['courseId' => $model->course->id])
                ->scheduledBetween($lessonFromDate, $lessonToDate)
                ->all();
            //lesson start date
            $changedFromTime       = (new \DateTime($model->course->fromTime))->format('H:i:s');
            $duration              = explode(':', $changedFromTime);
            $interval              = new \DateInterval('P1D');
            $startDate             = new \DateTime($model->course->startDate);
            $startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
            //lesson end date
            $rescheduleLessonsDays = count($rescheduleLessons) * 7;
            $endDate               = new \DateTime($model->course->startDate);
            $endDate->add(new \DateInterval('P'.$rescheduleLessonsDays.'D'));
            $endDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
            $period                = new \DatePeriod($startDate, $interval,
                $endDate);
            foreach ($period as $day) {
                if ((int) $day->format('N') === (int) $model->course->day) {
                    $lesson = new Lesson();
                    $lesson->setAttributes([
                        'courseId' => $model->course->id,
                        'teacherId' => $model->course->teacherId,
                        'status' => Lesson::STATUS_DRAFTED,
                        'date' => $day->format('Y-m-d H:i:s'),
                        'duration' => $model->course->duration,
                        'isDeleted' => false,
                    ]);
                    $lesson->save();
                }
            }
            return $this->redirect(['lesson/review', 'courseId' => $model->course->id,
                    'Course[lessonFromDate]' => $lessonFromDate->format('d-m-Y'),
                    'Course[lessonToDate]' => $lessonToDate->format('d-m-Y')]);
        } else {
            return $this->render('update',
                    [
                    'model' => $model->course,
                    'lastLessonDate' => $lastLessonDate,
                    'teacherDetails' => $teacherDetails,
                    'durationMinutes' => $durationMinutes,
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
                ->where(['courseId' => $model->course->id])
				->orderBy(['lesson.date' => SORT_ASC]),
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
