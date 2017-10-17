<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\User;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
USE yii\data\ActiveDataProvider;
/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherSubstituteController extends Controller
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
                'only' => ['index'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],	 
        ];
    }

    /**
     * Lists all TeacherAvailability models.
     *
     * @return mixed
     */
    public function actionIndex()
    {        
        $lessonIds = Yii::$app->request->get('ids');
        $teacherId = Yii::$app->request->get('teacherId');
        $lessons = Lesson::findAll($lessonIds);
        $programIds = [];
        foreach ($lessons as $lesson) {
            $programIds[] = $lesson->course->programId;
        }
        $teachers = User::find()
                ->teachers($programIds, Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->andWhere(['NOT', ['user.id' => end($lessons)->teacherId]])
                ->orderBy(['user_profile.firstname' => SORT_ASC])
                ->all();
        $query = Lesson::find()
                ->where(['id' => $lessonIds]);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $conflicts = [];
        $conflictedLessonIds = [];
        if ($teacherId) {
            foreach ($lessons as $lesson) {
                $lesson->setScenario('review');
                $lesson->teacherId = $teacherId;
                if (ActiveForm::validate($lesson)) {
                    $conflictedLessonIds[] = $lesson->id;
                    $conflicts[$lesson->id] = end(ActiveForm::validate($lesson));
                }
            }
        }
        $conflictedLessonIdsCount = count($conflictedLessonIds);
        $data = $this->renderAjax('_form', [
            'lessons' => $lessons,
            'teachers' => $teachers,
            'conflicts' => $conflicts,
            'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
            'conflictedLessonIds' => $conflictedLessonIds,
            'lessonDataProvider' => $lessonDataProvider
        ]);
        $response = [
            'status' => true,
            'data' => $data
        ];
        return $response;
    }
}
