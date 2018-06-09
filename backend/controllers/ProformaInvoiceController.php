<?php

namespace backend\controllers;

use Yii;

use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
/**
 * ProformaInvoiceController implements the CRUD actions for ProformaInvoice model.
 */
class ProformaInvoiceController extends BaseController
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => [
                    'create'
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [
                             'managePfi'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Invoice models.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $lessonIds = Yii::$app->request->get('ids');
        $invoiceIds = Yii::$app->request->get('teacherId');
        $lessons = Lesson::findAll($lessonIds);
        $invoices= ProformaInvoice::findAll($invoiceIds);

        
    }

    /**
     * Displays a single Invoice model.
     *
     * @param int $id
     *
     * @return mixed
     */
    }
