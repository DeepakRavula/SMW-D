<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\GroupLesson;
use yii\filters\ContentNegotiator;
use common\components\controllers\BaseController;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\discount\EnrolmentDiscount;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use Carbon\Carbon;
/**
 * PrivateLessonController implements the CRUD actions for PrivateLesson model.
 */
class GroupEnrolmentController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => [
                    'edit-discount', 'edit-end-date'
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
                            'edit-discount', 'edit-end-date'
                        ],
                        'roles' => ['managePrivateLessons'],
                    ],
                ],
            ],
        ];
    }

    public function actionEditDiscount($enrolmentId)
    {
        $model = Enrolment::findOne($enrolmentId);
        $model->setScenario(Enrolment::SCENARIO_EDIT);
        if (!$model->validate()) {
            return [
                'status' => false,
                'message' => ActiveForm::validate($model)['enrolment-courseid']
            ];
        }
        if ($model->hasGroupDiscount()) {
            $discount = $model->groupDiscount;
        } else {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $enrolmentId;
            $discount->discountType = EnrolmentDiscount::VALUE_TYPE_DOLLAR;
            $discount->type = EnrolmentDiscount::TYPE_GROUP;
        }
        $oldDiscount = $model->groupDiscount ? clone $model->groupDiscount : null;
        $data = $this->renderAjax('_form-apply-discount', [
            'model' => $model,
            'discount' => $discount
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            $discount->load($post);
            $discount->save();
            if (!$oldDiscount || ($oldDiscount->discount != $discount->discount || $oldDiscount->discountType != $discount->discountType)) {
                $model = Enrolment::findOne($enrolmentId);
                $model->resetGroupDiscount();
            }
            $response = [
                'status' => true,
            ];
        } else {
            $response = [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
    }
    public function actionEditEndDate($id) {
        $model = Enrolment::findOne($id);
        $model->setScenario(Enrolment::SCENARIO_GROUP_ENROLMENT_ENDDATE_ADJUSTMENT);
        $course = $model->course;
        if ($model->course->program->isGroup()) {
            $changedEndDate = Yii::$app->request->get('endDateTime');
            $lastLesson = $model->lastRootLesson;
            if (!$lastLesson) {
                return [
                    'status' => false,
                    'message' => 'There are no lessons in the enrolment so end date cannnot be adjusted.',
                ];
            }
            $lastLessonDate = Carbon::parse($lastLesson->date);
            $action = null;
            $dateRange = null;
            $previewDataProvider = null;
            if ($changedEndDate) {
                $date = Carbon::parse($changedEndDate);
                $objects = ['Lessons'];
                $results = [];
                if ($lastLessonDate > $date) {
                    $dateRange = $date->format('M d, Y') . ' - ' . $lastLessonDate->format('M d, Y');
                    $action = 'shrink';
                    foreach ($objects as $value) {
                        $results[] = [
                            'objects' => $value,
                            'action' => 'will be deleted',
                            'date_range' => 'within ' . $dateRange
                        ]; 
                    }
                } 
                $previewDataProvider = new ArrayDataProvider([
                    'allModels' => $results,
                    'sort' => [
                        'attributes' => ['objects', 'action', 'date_range'],
                    ],
                ]);
            }
            $post = Yii::$app->request->post();
            $endDate = Carbon::parse($course->endDate)->format('d-m-Y');
            $course->load(Yii::$app->getRequest()->getBodyParams(), 'Course');
           
            $post = Yii::$app->request->post();
            if ($post) {
                $course->load($post);
                    if ($model->load($post) && $model->validate()) {
                        $courseEndDate = $model->endDateTime;
                        $model->endDateTime = Carbon::parse($courseEndDate)->format('Y-m-d');
                        $lessons = GroupLesson::find()
                            ->andWhere(['group_lesson.enrolmentId' => $model->id])
                            ->joinWith(['lesson' => function ($query) use($courseEndDate) { 
                                $query->andWhere(['>', 'DATE(lesson.date)', Carbon::parse($courseEndDate)->format('Y-m-d')]);
                            }])
                            ->all();
                        $message = null;
                        $model->revertGroupLessonsCredit($lessons);
                        $model->save();
                        $message = 'Lesson credits has been credited to ' . $model->customer->publicIdentity . ' account.';
                        $model->setStatus();
                        $response = [
                            'status' => true,
                        ];
                    } else {
                        $errors = ActiveForm::validate($model);
                        $response = [
                            'error' => end($errors),
                            'status' => false,
                        ];
                    }
            } else {
                $data = $this->renderAjax('_form-schedule', [
                    'model' => $model,
                    'action' => $action,
                    'dateRange' => $dateRange,
                    'course' => $model->course,
                    'previewDataProvider' => $previewDataProvider
                ]);
                $response = [
                    'status' => true,
                    'data' => $data,
                ];
            }
        } else {
            $errors = ActiveForm::validate($course);
            $response = [
                'error' => end($errors),
                'status' => false,
            ];
        }
        return $response;
    }
}
