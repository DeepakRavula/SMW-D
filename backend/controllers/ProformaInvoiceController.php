<?php

namespace backend\controllers;

use Yii;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\AccessControl;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Invoice;
use common\models\ProformaInvoice;
use common\models\ProformaLineItem;
use common\components\controllers\BaseController;
use common\models\Location;
use common\models\User;
use common\models\UserProfile;
use common\models\UserEmail;
use common\models\Note;
use backend\models\search\ProformaInvoiceSearch;
use backend\models\search\PaymentFormLessonSearch;
use backend\models\search\PaymentFormGroupLessonSearch;
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
                    'create','note', 'update', 'delete'
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
                        'actions' => ['index','create','view','note', 'update', 'delete', 'create-payment-request'],
                        'roles' => [
                             'managePfi'
                        ]
                    ]
                ]
            ]
        ];
    }
    public function actionIndex()
    {
        $searchModel = new ProformaInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Lists all Invoice models.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $groupLessonSearchModel = new PaymentFormGroupLessonSearch();
        $searchModel = new PaymentFormLessonSearch();
        $model = new ProformaInvoice();
        $model->load(Yii::$app->request->get());
        $searchModel->load(Yii::$app->request->get());
        $groupLessonSearchModel->load(Yii::$app->request->get());
        $model->userId = $searchModel->userId;
        $user = User::findOne($model->userId);
        if ($searchModel->lessonIds || $model->invoiceIds || $groupLessonSearchModel->lessonIds) {
            $groupLessons = Lesson::findAll($groupLessonSearchModel->lessonIds);
            $lessons = Lesson::findAll($searchModel->lessonIds);
            $invoices = Invoice::findAll($model->invoiceIds);
            $model->locationId = $user->userLocation->location_id;
            $model->proforma_invoice_number = $model->getProformaInvoiceNumber();
            $model->save();
            if ($lessons) {
                foreach ($lessons as $lesson) {
                    $proformaLineItem = new ProformaLineItem();
                    $proformaLineItem->proformaInvoiceId = $model->id;
                    $proformaLineItem->lessonId = $lesson->id;
                    $proformaLineItem->save();
                }
            }
            if ($groupLessons) {
                foreach ($groupLessons as $lesson) {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $lesson->courseId])
                        ->customer($model->userId)
                        ->one();
                    $proformaLineItem = new ProformaLineItem();
                    $proformaLineItem->proformaInvoiceId = $model->id;
                    $proformaLineItem->lessonId = $lesson->id;
                    $proformaLineItem->enrolmentId = $enrolment->id;
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
            $model->save();
            $response = [
                'status' => true,
                'url' => Url::to(['proforma-invoice/view', 'id' => $model->id])
            ];
        } else {
            $response = [
                'status' => false,
                'errors' => 'Select any lesson or invoice to create PFI',
            ];
        }
        return $response;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new PaymentFormLessonSearch();
        $searchModel->showCheckBox = false;
        $groupLessonSearchModel = new PaymentFormLessonSearch();
        $groupLessonSearchModel->showCheckBox = false;
        $groupLessonSearchModel->userId = $model->userId;
        if ($model->userId) {
            $customer  = User::findOne(['id' => $model->userId]);
            $userModel = UserProfile::findOne(['user_id' => $customer->id]);
            $userEmail = UserEmail::find()
                ->joinWith(['userContact uc' => function ($query) use ($model) {
                    $query->andWhere(['uc.userId' => $model->userId]);
                }])
                ->one();
        }
        $lessonLineItems = Lesson::find()
            ->privateLessons()
            ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $groupLessonLineItems = Lesson::find()
            ->groupLessons()
            ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $groupLessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $groupLessonLineItems,
        ]);
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
            'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'noteDataProvider' => $noteDataProvider,
            'searchModel' => $searchModel,
            'groupLessonSearchModel' => $groupLessonSearchModel
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data  = $this->renderAjax('_details-form', [
            'model' => $model,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d');
            $model->dueDate = (new \DateTime($model->dueDate))->format('Y-m-d');
            if ($model->save()) {
                $response = [
                    'status'=>true,
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model),
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
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
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            return $this->redirect(['proforma-invoice/index']);
        }
    }
    public function actionCreatePaymentRequest()
    {
        $currentDate = (new \DateTime())->format('Y-m-d');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->isConfirmed()
            ->privateProgram()
            ->andWhere(['NOT', ['enrolment.paymentFrequencyId' => 0]])
            ->isRegular()
            ->joinWith(['course' => function ($query) use ($currentDate) {
                $query->andWhere(['>=', 'DATE(course.endDate)', $currentDate])
                        ->confirmed();
            }])
            ->all();
        
        foreach ($enrolments as $enrolment) {
            $date = null;
            $dateRange = $enrolment->getCurrentPaymentCycleDateRange($date);
            list($from_date, $to_date) = explode(' - ', $dateRange);
            $fromDate = new \DateTime($from_date);
            $toDate = new \DateTime($to_date);
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->privateLessons()
                ->program($enrolment->course->programId)
                ->between($fromDate, $toDate)
                ->student($enrolment->studentId)
                ->invoiced();
            $lessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->privateLessons()
                ->program($enrolment->course->programId)
                ->between($fromDate, $toDate)
                ->student($enrolment->studentId)
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->all();
            $lessonIds = [];
            foreach ($lessons as $lesson) {
                if ($lesson->isOwing($enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            if ($lessonIds) {
                $model = new ProformaInvoice();
                $model->userId = $enrolment->customer->id;
                $model->locationId = $enrolment->customer->userLocation->location_id;
                $model->proforma_invoice_number = $model->getProformaInvoiceNumber();
                $model->save();
                $lessons = Lesson::findAll($lessonIds);
                foreach ($lessons as $lesson) {
                    $proformaLineItem = new ProformaLineItem();
                    $proformaLineItem->proformaInvoiceId = $model->id;
                    $proformaLineItem->lessonId = $lesson->id;
                    $proformaLineItem->save();
                }
                $model->save();
            }
        }
    }
}
