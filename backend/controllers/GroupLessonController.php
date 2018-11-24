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
        $lineItemDiscount = $groupLesson->loadLineItemDiscount();
        $paymentFrequencyDiscount = $groupLesson->loadPaymentFrequencyDiscount();
        $customerDiscount = $groupLesson->loadCustomerDiscount();
        $multiEnrolmentDiscount = $groupLesson->loadMultiEnrolmentDiscount();
        $data = $this->renderAjax('_form-apply-discount', [
            'model' => $model,
            'groupLesson' => $groupLesson,
            'customerDiscount' => $customerDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'lineItemDiscount' => $lineItemDiscount,
            'multiEnrolmentDiscount' => $multiEnrolmentDiscount,
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            $lineItemDiscount->load($post);
            $customerDiscount->load($post);
            $lineItemDiscount->save();
            $customerDiscount->save();
            $paymentFrequencyDiscount->load($post);
            $multiEnrolmentDiscount->load($post);
            $paymentFrequencyDiscount->save();
            $multiEnrolmentDiscount->save();
            $model->save();
            $response = [
                'status' => true,
            ];
        } else {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
    }
}
