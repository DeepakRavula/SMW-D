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
        $lessonIds = Yii::$app->request->get('lessonIds');
        $invoiceIds = Yii::$app->request->get('invoiceIds');
        $lessons = Lesson::findAll($lessonIds);
        $invoices= Invoice::findAll($invoiceIds);
        $endLesson=end($lessons);
        $endInvoice=end($invoices);
        if(!empty($lessons)){
        $user=$endLesson->customer;
        }
        if(empty($user)){
            $user=$endInvoice->user;
        }
       
        
        if(!empty($lessonIds) || !empty($invoiceIds))
        {
            $proformaInvoice=new ProformaInvoice();
            $proformaInvoice->userId=$user->id;
            $proformaInvoice->locationId = $user->userLocation->location_id;
            $proformaInvoice->save();
            
if(!empty($lessons)){
            foreach($lessons as $lesson)
            {
                $proformaLineItem=new ProformaLineItem();
                $proformaLineItem->invoice_id=$proformaInvoice->id;
                $proformaLineItem->lessonId=$lesson->id;
                $proformaLineItem->save();
            }
        }
        if(!empty($invoices)){
            foreach($invoices as $invoice)
            {
                $proformaLineItem = new ProformaLineItem();
                $proformaLineItem->invoice_id = $proformaInvoice->id;
                $proformaLineItem->invoiceId = $invoice->id;
                $proformaLineItem->save();
                
            }

            return $this->redirect(['view', 'id' => $proformaInvoice->id]);
        }

        }
    }

        
    public function actionView($id)
    {
        $model                              = $this->findModel($id);
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
        $lessonLineItems=Lesson::find()
        ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.invoice_id'=>$model->id]);
            }]);
        }]);
       $lessonLineItemsDataProvider = new ActiveDataProvider([
        'query' => $lessonLineItems,
    ]);
    $invoiceLineItems=Invoice::find()
    ->joinWith(['proformaInvoiceItem' => function ($query) use ($model) {
            $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                $query->andWhere(['proforma_line_item.invoice_id'=>$model->id]);
        }]);
    }]);
   $invoiceLineItemsDataProvider = new ActiveDataProvider([
    'query' => $invoiceLineItems,
]);
        return $this->render('view',[
            'model' => $model,
            'customer' => $customer,
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'searchModel'=>$searchModel,
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
    
    /**
     * Displays a single Invoice model.
     *
     * @param int $id
     *
     * @return mixed
     */
    }
