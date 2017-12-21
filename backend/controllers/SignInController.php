<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/2/14
 * Time: 11:20 AM.
 */

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\AccountForm;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use yii\web\Controller;
use common\models\User;

class SignInController extends \common\components\backend\BackendController
{
    public $defaultAction = 'login';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

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

    public function actionLogin()
    {
        $this->layout = 'base';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user=$model->getUser();
            if($user->isStaff()) {
                return $this->redirect(['schedule/index']);
            }
            return $this->redirect(['dashboard/index']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProfile()
    {
        $model = Yii::$app->user->identity->userProfile;
        $user = Yii::$app->user->identity;
        $model->email = $user->email;
        if ($model->load($_POST) && $model->save() && $model->validate()) {
            $user->email = $model->email;
            $user->save();
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => Yii::t('backend', 'Your profile has been successfully saved', [], $model->locale),
            ]);

            return $this->refresh();
        }

        return $this->render('profile', ['model' => $model]);
    }

    public function actionAccount()
    {
        $user = Yii::$app->user->identity;
        $model = new AccountForm();
        $model->username = $user->username;
        $model->email = $user->email;
        if ($model->load($_POST) && $model->validate()) {
            $user->username = $model->username;
            $user->email = $model->email;
            if ($model->password) {
                $user->setPassword($model->password);
            }
            $user->save();
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => Yii::t('backend', 'Your account has been successfully saved'),
            ]);

            return $this->refresh();
        }

        return $this->render('account', ['model' => $model]);
    }

    /**
     * @return string|Response
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'base';
        $model = new PasswordResetRequestForm();
        $isEmailSent = false;
        $userName=$model->email;
        $primaryEmail = User::find()
                ->joinWith(['userContact' => function($query) use($userName) {
					$query->joinWith(['email' => function($query) use($userName){
						$query->andWhere(['email' => $userName]);
					}])
					->primary();
				}])
                ->notDeleted()
                ->one();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            if ($model->sendEmail() && !empty($primaryEmail)) {
                $isEmailSent = true;
            } else {
                 $model->addError('email', Yii::t('backend', 'Sorry, we are unable to reset password for email provided'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
            'isEmailSent' => $isEmailSent,
        ]);
    }

    /**
     * @param $token
     *
     * @return string|Response
     *
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'base';
        $tokenExpired = false;
        $model = null;
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            $tokenExpired = true;
        }

        $isResetPassword = false;
        if ($model && $model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            $isResetPassword = true;
        }

        return $this->render('resetPassword', [
            'model' => $model,
            'isResetPassword' => $isResetPassword,
            'tokenExpired' => $tokenExpired,
        ]);
    }
}
