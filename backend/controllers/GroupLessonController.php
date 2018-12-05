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

/**
 * PrivateLessonController implements the CRUD actions for PrivateLesson model.
 */
class GroupLessonController extends BaseController
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
                    'apply-discount'
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
                            'apply-discount'
                        ],
                        'roles' => ['managePrivateLessons'],
                    ],
                ],
            ],
        ];
    }

    public function actionApplyDiscount()
    {
        $groupLesson = new GroupLesson();
        $groupLesson->load(Yii::$app->request->get());
        $model = Lesson::findOne($groupLesson->lessonId);
        $model->enrolmentId = $groupLesson->enrolmentId;
        if (!$model->isEditable()) {
            return [
                'status' => false,
                'message' => 'Lesson is invoiced. You can\'t edit discount for this lessons',
            ];
        }
        $discount = $groupLesson->loadDiscount();
        $data = $this->renderAjax('_form-apply-discount', [
            'model' => $model,
            'groupLesson' => $groupLesson,
            'discount' => $discount
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            $discount->load($post);
            $discount->save();
            $model->save();
            return $this->redirect(['/enrolment/group-preview', 'enrolmentId' => $groupLesson->enrolmentId]);
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
    }
}
