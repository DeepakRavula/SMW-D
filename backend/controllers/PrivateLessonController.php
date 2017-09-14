<?php

namespace backend\controllers;

use Yii;
use common\models\PrivateLesson;
use backend\models\search\PrivateLessonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use common\models\PaymentCycleLesson;
use common\models\Lesson;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\LessonSplitUsage;
/**
 * PrivateLessonController implements the CRUD actions for PrivateLesson model.
 */
class PrivateLessonController extends Controller
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
			'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['merge'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all PrivateLesson models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrivateLessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrivateLesson model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PrivateLesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PrivateLesson();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PrivateLesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
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
     * Deletes an existing PrivateLesson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		if (($model->hasProFormaInvoice() && $model->proFormaInvoice->hasPayments()) || ($model->hasInvoice() && $model->invoice->hasPayments())) {
			$class = 'alert-danger';
			$link = Url::to(['lesson/view', 'id' => $model->id]);
			$message = 'Lesson has payments. You can\'t delete this lesson.';
		} else {
			$model->delete();
			$class = 'alert-success';
			$link = Url::to(['lesson/index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]);
			$message = 'Lesson has been deleted successfully';
		}

		Yii::$app->session->setFlash('alert', [
			'options' => ['class' => $class],
			'body' => $message,
                ]);
        return $this->redirect($link);
    }

	  public function actionSplit($id)
    {
        $model = $this->findModel($id);
        $lessonDurationSec = $model->durationSec;
        for ($i = 0; $i < $lessonDurationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC; $i++) {
            $lesson = clone $model;
            $lesson->isNewRecord = true;
            $lesson->id = null;
            $lesson->duration = Lesson::DEFAULT_MERGE_DURATION;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
            $duration = gmdate('H:i:s', Lesson::DEFAULT_EXPLODE_DURATION_SEC * ($i +1));
            $lessonDuration = new \DateTime($duration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration->format('H') . 'H' . $lessonDuration->format('i') . 'M'));
            $lesson->date = $date->format('Y-m-d H:i:s');
            $lesson->isExploded = true;
            $lesson->save();
            $paymentCycleLesson = new PaymentCycleLesson();
            $paymentCycleLesson->paymentCycleId = $model->paymentCycle->id;
            $paymentCycleLesson->lessonId = $lesson->id;
            $paymentCycleLesson->save();
            $privateLesson = clone $model->privateLesson;
            $privateLesson->isNewRecord = true;
            $privateLesson->id = null;
            $privateLesson->lessonId = $lesson->id;
            $privateLesson->save();
            $model->append($lesson);
        }
        $model->cancel();
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'The Lesson has been exploded successfully.',
        ]);
        return $this->redirect(['student/view', 'id' => $model->enrolment->student->id, '#'=> 'unscheduledLesson']);
    }
	
    public function actionMerge($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_EDIT);
        $post = Yii::$app->request->post();
        $additionalDuration = new \DateTime(Lesson::DEFAULT_MERGE_DURATION);
        $lessonDuration = new \DateTime($model->duration);
        $lessonDuration->add(new \DateInterval('PT' . $additionalDuration->format('H')
            . 'H' . $additionalDuration->format('i') . 'M'));
        $model->duration = $lessonDuration->format('H:i:s');
        if ($model->validate()) {
        $lessonSplitUsage = new LessonSplitUsage();
            $lessonSplitUsage->lessonId = $post['radioButtonSelection'];
            $lessonSplitUsage->extendedLessonId = $id;
            $lessonSplitUsage->mergedOn = (new \DateTime())->format('Y-m-d H:i:s');
            $lessonSplitUsage->save();
            $lesson = $this->findModel($lessonSplitUsage->lessonId);
            $lesson->cancel();
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'The Lesson has been extended successfully.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'errors' => $errors,
                'status' => false
            ];
        }
    }
    /**
     * Finds the PrivateLesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return PrivateLesson the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lesson::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
