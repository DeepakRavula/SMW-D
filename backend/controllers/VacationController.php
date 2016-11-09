<?php

namespace backend\controllers;

use Yii;
use common\models\Vacation;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Course;
use common\models\Enrolment;
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
        $model = new Vacation();
		$locationId = Yii::$app->session->get('location_id');
        if ($model->load(Yii::$app->request->post())) {
			$model->studentId = $studentId;
			$enrolment = Enrolment::find()
				->location($locationId)
				->where(['studentId' => $model->studentId])
				->notDeleted()
				->isConfirmed()
				->one();
			
			$model->courseId = $enrolment->courseId;
			$model->save();
            $model->on(Vacation::EVENT_PUSH, $model->pushLessons());

            return $this->redirect([
				'lesson/review',
				'courseId' => $model->courseId,
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
    public function actionDelete($id, $studentId)
    {
		$locationId = Yii::$app->session->get('location_id');
        $model = $this->findModel($id);
		$enrolment = Enrolment::find()
			->location($locationId)
			->where(['studentId' => $model->studentId])
			->notDeleted()
			->isConfirmed()
			->one();
	    $model->trigger(Vacation::EVENT_RESTORE);
        $model->on(Vacation::EVENT_RESTORE, $model->restoreLessons($model->fromDate, $model->toDate, $enrolment->courseId));
		
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
