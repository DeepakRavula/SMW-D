<?php

namespace backend\controllers;

use Yii;
use common\models\Vacation;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Course;
/**
 * VacationController implements the CRUD actions for Vacation model.
 */
class VacationController extends Controller
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
     * Lists all Vacation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Vacation::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vacation model.
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
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($studentId)
    {
		$session = Yii::$app->session;
		$request = Yii::$app->request;
        $model = new Vacation();
		$locationId = $session->get('location_id');
        if ($model->load($request->post())) {
			$model->studentId = $studentId;
			$enrolment = Enrolment::find()
				->location($locationId)
				->programs()
				->privateProgram()
				->andWhere(['studentId' => $model->studentId])
				->notDeleted()
				->isConfirmed()
				->one();
			Vacation::deleteAll([
				'studentId' => $studentId,
				'isConfirmed' => false,
			]);
			Lesson::deleteAll([
				'courseId' => $enrolment->course->id,
				'status' => Lesson::STATUS_DRAFTED
			]);
			$model->save();
            $model->on(Course::EVENT_VACATION_CREATE_PREVIEW, $enrolment->course->pushLessons($model->fromDate, $model->toDate));

            return $this->redirect([
				'lesson/review',
				'courseId' => $enrolment->course->id,
				'Vacation[id]' => $model->id,
				'Vacation[type]' => Vacation::TYPE_CREATE
			]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Vacation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing Vacation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
        $model = $this->findModel($id);
		$enrolment = Enrolment::find()
			->location($locationId)
			->programs()
			->privateProgram()
			->andWhere(['studentId' => $model->studentId])
			->notDeleted()
			->isConfirmed()
			->one();
		
		Lesson::deleteAll([
			'courseId' => $enrolment->courseId,
			'status' => Lesson::STATUS_DRAFTED
		]);
	    $model->trigger(Course::EVENT_VACATION_DELETE_PREVIEW);
        $model->on(Course::EVENT_VACATION_DELETE_PREVIEW, $enrolment->course->restoreLessons($model->fromDate, $model->toDate));
		
        return $this->redirect([
			'lesson/review',
			'courseId' => $enrolment->courseId,
			'Vacation[id]' => $model->id,
			'Vacation[type]' => Vacation::TYPE_DELETE
		]);
    }

    /**
     * Finds the Vacation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Vacation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
