<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use common\models\Lesson;
use common\models\Location;
use common\models\User;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\components\controllers\BaseController;
use yii\filters\ContentNegotiator;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;

/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherSubstitute1Controller extends BaseController
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
                'only' => [
                    'index', 'confirm', 'enrolment'
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
                        'actions' => [
                            'index', 'confirm', 'enrolment'
                        ],
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
                $attribute = 'lesson-date';
                if ($lesson->isExtra()) {
                    $attribute = 'extralesson-date';
                }
                if ($errors) {
                    if (current($errors[$attribute]) !== Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE) {
                        $conflictedLessonIds[] = $lesson->id;
                    }
                    $conflicts[$lesson->id] = $errors[$attribute];
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
                $newLessonIds[] = $newLesson->id;
                $newLesson->setScenario('substitute-teacher');
                $errors = ActiveForm::validate($newLesson);
                $attribute = 'lesson-date';
                if ($newLesson->isExtra()) {
                    $attribute = 'extralesson-date';
                }
                if ($errors) {
                    if (current($errors[$attribute]) !== Lesson::TEACHER_UNSCHEDULED_ERROR_MESSAGE) {
                        $conflictedLessonIds[] = $newLesson->id;
                    }
                    $conflicts[$newLesson->id] = $errors[$attribute];
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
                    ->andWhere(['id' => $newLessonIds]);
        }
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $conflictedLessonIdsCount = count($conflictedLessonIds);
        $lessonModel = current($lessons);
        $data = $this->renderAjax('_form', [
            'lessons' => $lessons,
            'teachers' => $teachers,
            'conflicts' => $conflicts,
            'newLessonIds' => $newLessonIds,
            'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
            'conflictedLessonIds' => $conflictedLessonIds,
            'lessonDataProvider' => $lessonDataProvider,
            'lessonModel' => $lessonModel
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
        $oldLessons = Lesson::findAll(Yii::$app->request->get('ids'));
        $lessons = Lesson::find()
                ->notDeleted()
                ->notConfirmed()
                ->andWhere(['createdByUserId' => Yii::$app->user->id])
                ->all();
        $lessonIds = [];
        foreach ($lessons as $i => $lesson) {
            $oldLesson = $oldLessons[$i];
            $oldLesson->cancel();
            $oldLesson->rescheduleTo($lesson, null);
            $lessonIds[] = $lesson->id;
            $lesson->isConfirmed = true;
            $lesson->save();
        }
        Lesson::triggerPusher();
        if (end($lessons)->isGroup()) {
            $courseModel = end($lessons)->course;
            $response = [
                'status' => true,
                'url' => Url::to(['/course/view', 'id' => $courseModel->id]),
            ];
        } else {
            $response = [
                'status' => true,
                'url' => Url::to(['/lesson/index-old', 'LessonSearchOld[type]' => true, 'LessonSearchOld[ids]' => $lessonIds])
            ];
        }
        return $response;
    }
}
