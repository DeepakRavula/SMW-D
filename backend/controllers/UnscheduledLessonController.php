<?php

namespace backend\controllers;

use Yii;
use backend\models\SystemLog;
use backend\models\search\SystemLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use backend\models\search\UnscheduledLessonSearch;
use yii\web\Response;
use common\models\UnscheduleLesson;
use common\models\Lesson;
use common\models\Note;

/**
 * LogController implements the CRUD actions for SystemLog model.
 */
class UnscheduledLessonController extends \common\components\controllers\BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => [
                    'bulk-unschedule', 'reason-to-unschedule'
                ],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['bulk-unschedule','index', 'reason-to-unschedule'
                        ],
                        'roles' => ['managePrivateLessons'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all SystemLog models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnscheduledLessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBulkUnschedule()
    {
            $unscheduleLessonModel = new UnscheduleLesson();
            $unscheduleLessonModel->setScenario(UnscheduleLesson::SCENARIO_BULK_UNSCHEDULE);
            $post  = Yii::$app->request->post();
            
            if ($unscheduleLessonModel->load(Yii::$app->request->get()) && $unscheduleLessonModel->validate()) {
                foreach ($unscheduleLessonModel->lessonIds as $lessonId) {
                    $model = $this->findModel($lessonId);
                    $model->unschedule($unscheduleLessonModel->reason);
                }
                Lesson::triggerPusher();
                $response = [
                    'status' => true,
                    'message' => 'Lessons unscheduled successfully',
                ];
            } else {
                $response = [
                    'status' => false,
                    'error' => $unscheduleLessonModel->getErrors('lessonIds'),
                ];
            }

        return $response;
 
    }

    public function actionReasonToUnschedule()
    {
        $unscheduleLessonModel = new UnscheduleLesson();
        $unscheduleLessonModel->setScenario(UnscheduleLesson::SCENARIO_BULK_UNSCHEDULE);
        $unscheduleLessonModel->load(Yii::$app->request->get());
        $data = $this->renderAjax('/lesson/_reason-to-unschedule', [
            'note' => new Note(),
            'unscheduleLessonModel' => $unscheduleLessonModel,
        ]);
            return [
                'status' => true,
                'data' => $data
            ];
 
    }

    protected function findModel($id)
    {
        if (($model = Lesson::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
