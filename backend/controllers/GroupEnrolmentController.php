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
        if ($model->course->program->isGroup() && $model->canDeleted()) {
            $lessons = Lesson::find()
                ->andWhere(['lesson.courseId' => $model->courseId])
                ->enrolment($id)
                ->isConfirmed()
                ->notCanceled()
                ->all();
            $message = null;
            $model->revertGroupLessonsCredit($lessons);
            $message = 'Lesson credits has been credited to ' . $model->customer->publicIdentity . ' account.';
            $model->deleteWithOutTransactionalData();
            $model->setStatus();
            $response = [
                'status' => true,
                'url' => Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]),
                'message' => $message
            ];
        } else {
            $response = [
                'status' => false
            ];
        }
        return $response;

    }
}
