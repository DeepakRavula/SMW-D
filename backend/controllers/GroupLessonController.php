<?php

namespace backend\controllers;

use Yii;
use common\models\RescheduleLesson;
use common\models\GroupLesson;
use backend\models\search\GroupLessonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GroupLessonController implements the CRUD actions for GroupLesson model.
 */
class GroupLessonController extends Controller
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
     * Lists all GroupLesson models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupLessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupLesson model.
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
     * Creates a new GroupLesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GroupLesson();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GroupLesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			$length = $model->groupCourse->length;
			$model->id = null;
           	$model->isNewRecord = true;
			$fromTime = \DateTime::createFromFormat('g:i A',$model->from_time);
			$model->from_time = $fromTime->format('H:i:s');
			$secs = strtotime($length) - strtotime("00:00:00");
			$toTime = date("H:i:s",strtotime($model->from_time) + $secs);
    	    $model->to_time = $toTime; 
			$lessonDate = \DateTime::createFromFormat('d-m-Y', $model->date);
            $model->date = $lessonDate->format('Y-m-d H:i:s');
           	$model->status = GroupLesson::STATUS_SCHEDULED; 
            $model->save();
			
			$lessonRescheduleModel = new RescheduleLesson();
            $lessonRescheduleModel->lesson_id = $id;
            $lessonRescheduleModel->reschedule_lesson_id = $model->id;    
            $lessonRescheduleModel->type = RescheduleLesson::TYPE_GROUP_LESSON;    
            $lessonRescheduleModel->save();
			$rescheduleLessonId = $model->id;
			
			$model = $this->findModel($id);
            $model->status = GroupLesson::STATUS_CANCELED;
            $model->save();
			
        	return $this->redirect(['view', 'id' => $rescheduleLessonId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GroupLesson model.
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
     * Finds the GroupLesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GroupLesson the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = GroupLesson::find()->location($locationId)
			->where(['group_lesson.id' => $id])->one();
				if ($model !== null) {
					return $model;
				} else {
					throw new NotFoundHttpException('The requested page does not exist.');
				}
    }
}
