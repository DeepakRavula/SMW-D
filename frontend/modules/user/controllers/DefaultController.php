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
use common\models\Address;
use common\models\PhoneNumber;
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
        $user = User::findOne(['id' => $id]);
        $model->setModel($user);	
		if ($model->load($request->post())) {
			if($model->save()) {
				return [
				   'status' => true,
				];	
			} else {
				$errors = ActiveForm::validate($model);
                return [
                    'status' => false,
                    'errors' => current($errors)
                ];
			}
		}
	}
	public function actionEditPhone($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
		$user = User::findOne(['id' => $id]);
        $model->setModel($user);
        $phoneNumberModels = $model->phoneNumbers;
		$data = $this->renderAjax('update/_phone', [
			'model' => $model,
			'phoneNumberModels' => $phoneNumberModels,
		]);
		
        $response = Yii::$app->response;
        if ($request->isPost) {
            $oldPhoneIDs = ArrayHelper::map($phoneNumberModels, 'id', 'id');
            $phoneNumberModels = UserForm::createMultiple(PhoneNumber::classname(), $phoneNumberModels);
            Model::loadMultiple($phoneNumberModels, $request->post());
            $deletedPhoneIDs = array_diff($oldPhoneIDs, array_filter(ArrayHelper::map($phoneNumberModels, 'id', 'id')));

            $valid = Model::validateMultiple($phoneNumberModels);
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
					if (!empty($deletedPhoneIDs)) {
						PhoneNumber::deleteAll(['id' => $deletedPhoneIDs]);
					}
					foreach ($phoneNumberModels as $phoneNumberModel) {
						$phoneNumberModel->user_id = $id;
						if (!($flag = $phoneNumberModel->save(false))) {
							$transaction->rollBack();
							break;
						}
					}
                    if ($flag) {
                        $transaction->commit();
                       	return [
							'status' => true,
						]; 
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        } else {
			return [
				'status' => true,
				'data' => $data,
			];
		}
	}
	public function actionEditAddress($id)
	{
		$request = Yii::$app->request;
		$model = new UserForm();
		$user = User::findOne(['id' => $id]);
        $model->setModel($user);
        $addressModels = $model->addresses;
		$data = $this->renderAjax('update/_address', [
			'model' => $model,
			'addressModels' => $addressModels,
		]);
		
        if ($request->isPost) {
            $oldAddressIDs = ArrayHelper::map($addressModels, 'id', 'id');
            $addressModels = UserForm::createMultiple(Address::classname(), $addressModels);
            Model::loadMultiple($addressModels, $request->post());
            $deletedAddressIDs = array_diff($oldAddressIDs, array_filter(ArrayHelper::map($addressModels, 'id', 'id')));

            $valid = Model::validateMultiple($addressModels);
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
					if (!empty($deletedAddressIDs)) {
						Address::deleteAll(['id' => $deletedAddressIDs]);
					}
					foreach ($addressModels as $addressModel) {
						if (!($flag = $addressModel->save(false))) {
							$transaction->rollBack();
							break;
						}
						$model->getModel()->link('addresses', $addressModel);
					}
                    if ($flag) {
                        $transaction->commit();
                       	return [
							'status' => true,
						]; 
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        } else {
			return [
				'status' => true,
				'data' => $data,
			];
		}
	}
}
