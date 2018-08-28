<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use backend\models\EnrolmentForm;
use yii\filters\ContentNegotiator;
use yii\widgets\ActiveForm;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\CustomerReferralSource;

/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class ReverseEnrolmentController extends BaseController
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
                'only' => ['add-customer', 'add-student'],
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
                        'actions' => ['add-customer', 'add-student'],
                        'roles' => ['manageEnrolments'],
                    ],
                ],
            ], 
        ];
    }

    public function actionAddCustomer()
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $courseDetail = new EnrolmentForm();
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        $data = $this->renderAjax('/enrolment/new/_form-customer', [
            'courseDetail' => $courseDetail,
        ]);
        $courseDetail->setScenario(EnrolmentForm::SCENARIO_CUSTOMER);
        if (Yii::$app->request->post()) {
            if ($courseDetail->load(Yii::$app->request->post()) && $courseDetail->validate()) {
                $courseDetail->setScenario(EnrolmentForm::SCENARIO_STUDENT);
                $courseDetail->last_name = $courseDetail->lastname;
                $studentData = $this->renderAjax('/enrolment/new/_form-student', [
                    'courseDetail' => $courseDetail
                ]);
                $response = [
                    'status' => true,
                    'data' => $studentData
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseDetail)
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

    public function actionAddStudent()
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $courseDetail = new EnrolmentForm();
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        $courseDetail->setScenario(EnrolmentForm::SCENARIO_STUDENT);
        $data = $this->renderAjax('/enrolment/new/_form-student', [
            'courseDetail' => $courseDetail
        ]);
        if (Yii::$app->request->post()) {
            if ($courseDetail->load(Yii::$app->request->post()) && $courseDetail->validate()) {
                $response = [
                    'status' => true,
                    'url' => \yii\helpers\Url::to(['enrolment/add', 'EnrolmentForm' => $courseDetail])
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseDetail)
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
}
