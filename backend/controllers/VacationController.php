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
use yii\web\Response;
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
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['create'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
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
    public function actionCreate($enrolmentId)
    {
		$enrolment = Enrolment::findOne(['id' => $enrolmentId]);
		$data = $this->renderAjax('/student/vacation/_form', [
			'model' => new Vacation(),
			'enrolmentId' => $enrolmentId,
		]);
		
		$request = Yii::$app->request;
        $model = new Vacation();
        if ($model->load($request->post())) {
			$db = Yii::$app->db;
            $transaction = $db->beginTransaction();
			Vacation::deleteAll([
				'enrolmentId' => $enrolmentId,
				'isConfirmed' => false,
			]);
			Lesson::deleteAll([
				'courseId' => $enrolment->course->id,
				'status' => Lesson::STATUS_DRAFTED
			]);
            $transaction->commit();
			$model->enrolmentId = $enrolmentId;
			if($model->save()) {
				$model->on(Course::EVENT_VACATION_CREATE_PREVIEW, $enrolment->course->pushLessons($model->fromDate, $model->toDate));

				return $this->redirect([
					'lesson/review',
					'courseId' => $enrolment->course->id,
					'Vacation[id]' => $model->id,
					'Vacation[type]' => Vacation::TYPE_CREATE
				]);
			}
        } else {
            return [
				'status' => true,
				'data' => $data,
			]; 
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
        $model = $this->findModel($id);
		Lesson::deleteAll([
			'courseId' => $model->enrolment->courseId,
			'status' => Lesson::STATUS_DRAFTED
		]);
	    $model->trigger(Course::EVENT_VACATION_DELETE_PREVIEW);
        $model->on(Course::EVENT_VACATION_DELETE_PREVIEW, $model->enrolment->course->restoreLessons($model->fromDate, $model->toDate));
		
        return $this->redirect([
			'lesson/review',
			'courseId' => $model->enrolment->courseId,
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
