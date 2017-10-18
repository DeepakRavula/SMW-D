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
use yii\widgets\ActiveForm;
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
        $newLessonIds = [];
        $draftLessons = Lesson::find()
                ->select('id')
                ->notConfirmed()
                ->andWhere(['createdByUserId' => Yii::$app->user->id])
                ->all();
        if ($draftLessons) {
            Lesson::deleteAll(['id' => $draftLessons]);
        }
        $conflicts = [];
        foreach ($lessons as $lesson) {
            if ($teacherId) {
                $newLesson = clone $lesson;
                $newLesson->isNewRecord = true;
                $newLesson->id = null;
                $newLesson->teacherId = $teacherId;
                $newLesson->isConfirmed = false;
                $newLesson->save();
                $newLessonIds[] = $newLesson->id;
                $newLesson->setScenario('substitute-teacher');
                if (ActiveForm::validate($newLesson)) {
                    $conflictedLessonIds[] = $newLesson->id;
                    $errors = ActiveForm::validate($newLesson);
                    $conflicts[$newLesson->id] = $errors['lesson-date'];
                }
                $query = Lesson::find()
                    ->notConfirmed()
                    ->andWhere(['createdByUserId' => Yii::$app->user->id]);
            }
            $programIds[] = $lesson->course->programId;
        }
        $teachers = User::find()
                ->teachers($programIds, Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->andWhere(['NOT', ['user.id' => end($lessons)->teacherId]])
                ->orderBy(['user_profile.firstname' => SORT_ASC])
                ->all();
        $conflictedLessonIds = [];
        if (!$teacherId) {
            $query = Lesson::find()
                    ->where(['id' => $newLessonIds]);
        }
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $conflictedLessonIdsCount = count($conflictedLessonIds);
        $data = $this->renderAjax('_form', [
            'lessons' => $lessons,
            'teachers' => $teachers,
            'conflicts' => $conflicts,
            'newLessonIds' => $newLessonIds,
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
    
    public function actionLesson($id)
    {
        $model = $this->findModel($id);
        $existingDate = $model->date;
        $data = $this->renderAjax('lesson/_form', [
            'model' => $model,
        ]);
        $response = [
            'status' => true,
            'data' => $data
        ];
        if ($model->load(Yii::$app->request->post()) && !empty($model->applyContext)) {
            if($model->isResolveSingleLesson()) {
                $response = $this->resolveSingleLesson($model, $existingDate);
            } else {
                $conflictedLessons = $this->fetchConflictedLesson($model->course);
                $response = $this->resolveAllLesson($conflictedLessons, $model);
            }
        } 
        return $response;
    }
}
