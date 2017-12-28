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
       
        return $this->render('view', [
            'model' => $model,
        ]);
    }
		public function actionEditProfile($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
        $model->setModel($this->findModel($id));	
		$userProfile  = $model->getModel()->userProfile;
		if ($model->load($request->post()) && $userProfile->load($request->post())) {
			if($model->save()) {
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
                ->where(['user.id' => $id])
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
}
