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
                'only' => ['merge', 'update-attendance'],
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
        $model->privateLesson->split();
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
        $splitLesson = $this->findModel($post['radioButtonSelection']);
        if ($model->validate()) {
            $splitLesson->privateLesson->merge($model);
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
	
	public function actionUpdateAttendance($id)
	{
        $model = $this->findModel($id);
		$post = Yii::$app->request->post();
		if($model->load($post) && $model->save()) {
			return [
				'status' => true,
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
