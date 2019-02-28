<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\Course;
use common\models\Enrolment;
use common\models\Location;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Student;
use common\models\ExtraLesson;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\log\LessonLog;
use common\models\User;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use common\components\controllers\BaseController;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class ExtraLessonController extends BaseController
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
                        'roles' => ['managePrivateLessons', 'manageGroupLessons'],
                    ],
                ]
            ] 
        ];
    }

    /**
     * Lists all Lesson models.
     *
     * @return mixed
     */
    public function actionCreatePrivate($studentId)
    {
        $model = new ExtraLesson();
        $model->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model->setScenario(Lesson::SCENARIO_CREATE);
        $request = Yii::$app->request;
        $studentModel = Student::findOne($studentId);
        $model->studentId = $studentModel->id;
        $model->programId = !empty($studentModel->firstPrivateCourse) ? $studentModel->firstPrivateCourse->programId : null;
        $model->teacherId = !empty($studentModel->firstPrivateCourse) ? $studentModel->firstPrivateCourse->teacherId : null;
        $data = $this->renderAjax('/student/_form-lesson', [
            'model' => $model,
            'studentModel' => $studentModel
        ]);
        if ($model->load($request->post())) {
            $model->addPrivate(Lesson::STATUS_SCHEDULED);
            if ($model->save()) {
                $model->makeAsRoot();
                
                $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                $model->on(
                    Lesson::EVENT_AFTER_INSERT,
                    [new LessonLog(), 'extraLessonCreate'],
                    ['loggedUser' => $loggedUser]
                );
                $model->trigger(Lesson::EVENT_AFTER_INSERT);
                Lesson::triggerPusher();
                $response   = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
                ];
            } else {
                $response   = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionValidatePrivate($studentId)
    {
        $model = new ExtraLesson();
        $model->type = Lesson::TYPE_EXTRA;
        $model->status = Lesson::STATUS_SCHEDULED;
        $model->setScenario(Lesson::SCENARIO_CREATE);
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            $studentEnrolment = Enrolment::find()
               ->joinWith(['course' => function ($query) use ($model) {
                   $query->andWhere(['course.programId' => $model->programId])
                           ->confirmed()
                           ->notDeleted();
               }])
                ->andWhere(['studentId' => $studentId])
                ->one();
            $model->courseId = !empty($studentEnrolment) ? $studentEnrolment->courseId : null;
            $model->studentId = $studentId;
            return  ActiveForm::validate($model);
        }
    }
    
    public function actionCreateGroup($courseId)
    {
        $course = Course::findOne($courseId);
        $model = new ExtraLesson();
        $model->status = Lesson::STATUS_SCHEDULED;
        $model->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model->setScenario(Lesson::SCENARIO_CREATE_GROUP);
        $request = Yii::$app->request;
        $model->courseId = $courseId;
        $model->programId = $course->programId;
        $model->teacherId = $course->teacherId;
        $data = $this->renderAjax('_form-group-lesson', [
            'model' => $model,
            'course' => $course
        ]);
        if ($model->load($request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $model->dueDate = (new \DateTime($model->date))->format('Y-m-d');
            $model->isConfirmed = true;
            $course = $model->addGroup();
            $model->courseId = $course->id;
            if ($model->save()) {
                $model->makeAsRoot();
                Lesson::triggerPusher();
                $response   = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
                ];
            } else {
                $response   = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionValidateGroup($courseId)
    {
        $course = Course::findOne($courseId);
        $model = new ExtraLesson();
        $model->programId = $course->programId;
        $model->type = Lesson::TYPE_EXTRA;
        $model->status = Lesson::STATUS_SCHEDULED;
        $model->setScenario(Lesson::SCENARIO_CREATE_GROUP);
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            return  ActiveForm::validate($model);
        }
    }
    
    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Lesson::find()->location($locationId)
            ->andWhere(['lesson.id' => $id, 'isDeleted' => false])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
