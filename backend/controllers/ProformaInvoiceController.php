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
use common\models\Lesson;
use common\models\Invoice;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;
use common\models\ProformaItemLesson;
use common\models\ProformaItemInvoice;
use common\components\controllers\BaseController;
use common\models\Location;
use common\models\InvoiceLineItem;
use common\models\User;
use common\models\UserProfile;
use common\models\UserEmail;
use common\models\Note;
use backend\models\search\ProformaInvoiceSearch;
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
                    'create',
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
                        'actions' => ['create','view'],
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
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $searchModel = new ProformaInvoiceSearch();
        $searchModel->showCheckBox = true;
        $model = new ProformaInvoice();
        $currentDate = new \DateTime();
        $model->date = $currentDate->format('M d,Y');
        $model->fromDate = $currentDate->format('M 1,Y');
        $model->toDate = $currentDate->format('M t,Y'); 
        $model->dateRange = $model->fromDate . ' - ' . $model->toDate;
        $proformaInvoiceData = Yii::$app->request->get('ProformaInvoice');
        if ($proformaInvoiceData) {
            $model->load(Yii::$app->request->get());
            list($model->fromDate, $model->toDate) = explode(' - ', $model->dateRange);
            if ($model->lessonId) {
                $lesson = Lesson::findOne($model->lessonId);
                $model->userId = $lesson->customer->id;
            }
        }
	    $fromDate = new \DateTime($model->fromDate);
        $toDate = new \DateTime($model->toDate);
        $lessonsQuery = Lesson::find();
        if ($model->lessonIds) {
            $lessonsQuery->andWhere(['id' => $model->lessonIds]);
        } else {
            $lessonsQuery->notDeleted()
                ->between($fromDate, $toDate)
                ->privateLessons()
                ->customer($model->userId)
                ->isConfirmed()
                ->notCanceled()
                ->unInvoiced()
                ->nonPfi()
                ->location($locationId);
            $allLessons = $lessonsQuery->all();
            $lessonIds = [];
            foreach ($allLessons as $lesson) {
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            $lessonsQuery = Lesson::find()
                ->andWhere(['id' => $lessonIds]);
        }
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonsQuery
        ]);
        $invoicesQuery = Invoice::find();
        if ($model->invoiceIds) {
            $invoicesQuery->andWhere(['id' => $model->invoiceIds]);
        } else {
            $invoicesQuery->notDeleted()
                ->lessonInvoice()
                ->location($locationId)
                ->customer($model->userId)
                ->nonPfi()
                ->unpaid();
        }
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoicesQuery
        ]);
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if($model->lessonIds || $model->invoiceIds) {
                $lessons = Lesson::findAll($model->lessonIds);
                $invoices = Invoice::findAll($model->invoiceIds);
                $endLesson = end($lessons);
                $endInvoice = end($invoices);
                if ($lessons) {
                    $user = $endLesson->customer;
                }
                if (!$user) {
                    $user = $endInvoice->user;
                }
                $model->userId = $user->id;
                $model->locationId = $user->userLocation->location_id;
                $model->save();
                if ($lessons) {
                    foreach ($lessons as $lesson) {
                        $proformaLineItem = new ProformaLineItem();
                        $proformaLineItem->proformaInvoiceId = $model->id;
                        $proformaLineItem->lessonId = $lesson->id;
                        $proformaLineItem->save();
                    }
                }
                if ($invoices) {
                    foreach ($invoices as $invoice) {
                        $proformaLineItem = new ProformaLineItem();
                        $proformaLineItem->proformaInvoiceId = $model->id;
                        $proformaLineItem->invoiceId = $invoice->id;
                        $proformaLineItem->save();
                    }
                }
            }
            $response = [
                'status' => true,
                'url' => Url::to(['proforma-invoice/view', 'id' => $model->id])
            ];
        } else {
            $data = $this->renderAjax('/receive-payment/_create-pfi', [
                'model' => $model,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel'=> $searchModel,
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ProformaInvoiceSearch();
        $searchModel->showCheckBox = false;
        if (!empty($model->userId)) {
            $customer  = User::findOne(['id' => $model->userId]);
            $userModel = UserProfile::findOne(['user_id' => $customer->id]);
            $userEmail = UserEmail::find()
                ->joinWith(['userContact uc' => function ($query) use ($model) {
                    $query->andWhere(['uc.userId' => $model->userId]);
                }])
                ->one();
        }
        $lessonLineItems = Lesson::find()
            ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonLineItems,
        ]);
        $invoiceLineItems = Invoice::find()
            ->joinWith(['proformaInvoiceItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);
        $notes = Note::find()
        ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_PROFORMA])
        ->orderBy(['createdOn' => SORT_DESC]);

    $noteDataProvider = new ActiveDataProvider([
        'query' => $notes,
    ]);
        return $this->render('view', [
            'model' => $model,
            'customer' => $customer,
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'noteDataProvider'=>$noteDataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = ProformaInvoice::find()
                ->andWhere([
                    'id' => $id,
                    'locationId' => $locationId,
                ])
                ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionNote($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => true,
            ];
        }
    }
}