<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use common\models\Lesson;
use common\models\LessonHierarchy;
use common\models\Location;
use common\models\User;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use yii\filters\ContentNegotiator;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;

/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherSubstituteController extends BaseController
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
                'only' => ['index', 'confirm'],
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
                        'actions' => ['index', 'confirm'],
                        'roles' => ['managePrivateLessons', 'manageGroupLessons'],
                    ],
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
        $resolvingConflict = Yii::$app->request->get('resolvingConflicts');
        $programIds = [];
        $newLessonIds = [];
        $draftLessons = Lesson::find()
                ->select(['id', 'type'])
                ->notDeleted()
                ->notConfirmed()
                ->andWhere(['createdByUserId' => Yii::$app->user->id])
                ->all();
        if ($draftLessons && !$resolvingConflict) {
            LessonHierarchy::deleteAll(['childLessonId' => $draftLessons]);
            Lesson::deleteAll(['id' => $draftLessons]);
        }
        $conflicts = [];
        $conflictedLessonIds = [];
        if ($resolvingConflict) {
            foreach ($draftLessons as $lesson) {
                $lesson = Lesson::findOne($lesson->id);
                $lesson->setScenario('substitute-teacher');
                $newLessonIds[] = $lesson->id;
                $errors = ActiveForm::validate($lesson);
                if ($errors) {
                    if (current($errors['lesson-date']) !== Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE) {
                        $conflictedLessonIds[] = $lesson->id;
                    }
                    $conflicts[$lesson->id] = $errors['lesson-date'];
                }
            }
        }
        $status = true;
        foreach ($lessons as $lesson) {
            if ($teacherId && !$resolvingConflict) {
                $newLesson = clone $lesson;
                $newLesson->isNewRecord = true;
                $newLesson->id = null;
                $newLesson->teacherId = $teacherId;
                $newLesson->isConfirmed = false;
                $newLesson->save();
                $newLesson->makeAsRoot();
                $lesson->rescheduleTo($newLesson);
                $newLessonIds[] = $newLesson->id;
                $newLesson->setScenario('substitute-teacher');
                $errors = ActiveForm::validate($newLesson);
                if ($errors) {
                    if (current($errors['lesson-date']) !== Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE) {
                        $conflictedLessonIds[] = $newLesson->id;
                    }
                    $conflicts[$newLesson->id] = $errors['lesson-date'];
                }
            }
            $programIds[] = $lesson->course->programId;
            if (current($lessons)->teacherId !== $lesson->teacherId) {
                $status = false;
            }
        }
        $query = Lesson::find()
                    ->notDeleted()
                    ->notConfirmed()
                    ->andWhere(['createdByUserId' => Yii::$app->user->id]);
        $teachers = User::find()
                ->teachers($programIds, Location::findOne(['slug' => \Yii::$app->location])->id)
                ->join('LEFT JOIN', 'user_profile', 'user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->andWhere(['NOT', ['user.id' => end($lessons)->teacherId]])
                ->orderBy(['user_profile.firstname' => SORT_ASC])
                ->all();
        
        if (!$teacherId) {
            $query = Lesson::find()
                    ->notDeleted()
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
            'status' => $status,
            'data' => $data,
            'hasConflicts' => $conflictedLessonIdsCount > 0 ? true : false,
        ];
        return $response;
    }
    
    public function actionConfirm()
    {
        $oldLessons = Yii::$app->request->get('ids');
        foreach ($oldLessons as $lesson) {
            $oldLesson = Lesson::findOne($lesson);
            $oldLesson->Cancel();
        }
        $lessons = Lesson::find()
                ->notConfirmed()
                ->andWhere(['createdByUserId' => Yii::$app->user->id])
                ->all();
        $lessonIds = [];
        foreach ($lessons as $lesson) {
            $lessonIds[] = $lesson->id;
            $lesson->isConfirmed = true;
            $lesson->save();
        }
        $response = [
            'status' => true,
            'url' => Url::to(['/lesson/index', 'LessonSearch[type]' => true, 'LessonSearch[ids]' => $lessonIds])
        ];
        return $response;
    }
}
