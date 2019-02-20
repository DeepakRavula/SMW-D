<?php

namespace frontend\modules\user\controllers;

use common\base\MultiModel;
use frontend\modules\user\models\AccountForm;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\User;
use frontend\models\UserForm;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use common\models\UserProfile;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use yii\data\ActiveDataProvider;

class DefaultController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                },
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['edit-profile', 'edit-phone', 'edit-address'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $accountForm = new AccountForm();
        $accountForm->setUser(Yii::$app->user->identity);

        $model = new MultiModel([
            'models' => [
                'account' => $accountForm,
                'profile' => Yii::$app->user->identity->userProfile,
            ],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $locale = $model->getModel('profile')->locale;
            Yii::$app->session->setFlash('forceUpdateLocale');
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => Yii::t('frontend', 'Your account has been successfully saved', [], $locale),
            ]);

            return $this->refresh();
        }

        return $this->render('index', ['model' => $model]);
    }
    public function actionUpdate()
    {
        $id = Yii::$app->user->id;
        $model = User::findOne(['id' => $id]);
        $request = Yii::$app->request;
        $invoiceSearchModel = new InvoiceSearch();
        $invoiceSearchModel->dateRange = (new\DateTime())->format('M d,Y') . ' - ' . (new\DateTime())->format('M d,Y');
        if ($invoiceSearchModel->load($request->get())) {
            list($invoiceSearchModel->fromDate, $invoiceSearchModel->toDate) = explode(' - ', $invoiceSearchModel->dateRange);
        }
        return $this->render('view', [
            'model' => $model,
            'searchModel' => $invoiceSearchModel,
            'invoicedLessonsDataProvider' => $this->getTimeVoucherDataProvider($id, $invoiceSearchModel->fromDate, $invoiceSearchModel->toDate,$invoiceSearchModel->summariseReport),
        ]);
    }
    public function actionEditProfile($id)
    {
        $request = Yii::$app->request;
        $model = new UserForm();
        $model->setModel($this->findModel($id));
        $userProfile  = $model->getModel()->userProfile;
        if ($userProfile->load($request->post())) { 
            if ($model->save()) {
                $userProfile->setScenario(UserProfile::SCENARIO_FRONT_END_USER_EDIT);
                $userProfile->save();
                return [
                   'status' => true,
                ];
            } else {
                $errors = ActiveForm::validate($model);
                return [
                    'status' => false,
                    'errors' => $errors
                ];
            }
        }
    }
    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $lastRole = end($roles);
        $adminModel = User::findOne(['id' => $id]);
        $model = User::find()->location($locationId)
                ->andWhere(['user.id' => $id])
                ->notDeleted()
                ->one();
        if ($model !== null) {
            return $model;
        } elseif ($lastRole->name === User::ROLE_ADMINISTRATOR && $adminModel != null) {
            return $adminModel;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function getTimeVoucherDataProvider($id, $fromDate, $toDate, $summariseReport)
    {
        
        $timeVoucher = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($fromDate,$toDate) {
                $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE])
                    ->between((new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d'));
            }])
            ->joinWith(['lesson' => function ($query) use ($id) {
                $query->andWhere(['lesson.teacherId' => $id])
                ->groupBy('lesson.id');
            }])
           ->orderBy(['invoice.date' => SORT_ASC]);
        if($summariseReport) { 
            $timeVoucher->groupBy('invoice.date');
        }    
        return new ActiveDataProvider([
            'query' => $timeVoucher,
            'pagination' => false,
        ]);
    }
}
