<?php

namespace frontend\modules\user\controllers;

use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserToken;
use frontend\modules\user\models\LoginForm;
use common\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class SignInController.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SignInController extends \yii\web\Controller
{

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
                        'actions' => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'activation',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'activation',
                        ],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/user/default/index']);
                        },
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }


    /**
     * @return array|string|Response
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/schedule/index']);
        } else {
            return $this->render('login', [
                    'model' => $model,
            ]);
        }
    }

    /**
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                if ($model->shouldBeActivated()) {
                    Yii::$app->getSession()->setFlash('alert', [
                        'body' => Yii::t(
                            'frontend',
                            'Your account has been successfully created. Check your email for further instructions.'
                        ),
                        'options' => ['class' => 'alert-success'],
                    ]);
                } else {
                    Yii::$app->getUser()->login($user);
                }

                return $this->goHome();
            }
        }

        return $this->render('signup', [
                'model' => $model,
        ]);
    }

    public function actionActivation($token)
    {
        $token = UserToken::find()
            ->byType(UserToken::TYPE_ACTIVATION)
            ->byToken($token)
            ->notExpired()
            ->one();

        if (!$token) {
            throw new BadRequestHttpException();
        }

        $user = $token->user;
        $user->updateAttributes([
            'status' => User::STATUS_ACTIVE,
        ]);
        $token->delete();
        Yii::$app->getUser()->login($user);
        Yii::$app->getSession()->setFlash('alert', [
            'body' => Yii::t('frontend', 'Your account has been successfully activated.'),
            'options' => ['class' => 'alert-success'],
        ]);

        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionRequestPasswordReset()
    {
        // return $this->redirect(['user/sign-in']);
        // exit;
        $model = new PasswordResetRequestForm();
        $isEmailSent = false;
        \Yii::$app->session->remove('captcha-error');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $captchaSecretCode = '6Le5FPoZAAAAAIVHMoYZmozeJaX7jwSEAntJEWjQ';
		    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$captchaSecretCode."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
            if($response['success'] == true)
			{  
                \Yii::$app->session->remove('captcha-error');
                if ($model->sendEmail()) {
                    $isEmailSent = true;
                } else {
                    Yii::$app->session->setFlash('alert', [
                        'body' => Yii::t('backend', 'Sorry, we are unable to reset password for email provided.'),
                        'options' => ['class' => 'alert-danger'],
                    ]);
                }
            }else{
                \Yii::$app->session->set('captcha-error', 'Please verify that you are not a robot.');
                return $this->render('requestPasswordResetToken', [
                    'model' => $model,
                    'isEmailSent' => $isEmailSent,
                ]);
            }
        }else{
            return $this->render('requestPasswordResetToken', [
                'model' => $model,
                'isEmailSent' => $isEmailSent,
            ]);
        }

       
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

    /**
     * @param $client \yii\authclient\BaseClient
     *
     * @return bool
     *
     * @throws Exception
     */
    public function successOAuthCallback($client)
    {
        // use BaseClient::normalizeUserAttributeMap to provide consistency for user attribute`s names
        $attributes = $client->getUserAttributes();
        $user = User::find()->andWhere([
                'oauth_client' => $client->getName(),
                'oauth_client_user_id' => ArrayHelper::getValue($attributes, 'id'),
            ])
            ->notDeleted()
            ->one();
        if (!$user) {
            $user = new User();
            $user->scenario = 'oauth_create';
            $user->username = ArrayHelper::getValue($attributes, 'login');
            $user->email = ArrayHelper::getValue($attributes, 'email');
            $user->oauth_client = $client->getName();
            $user->oauth_client_user_id = ArrayHelper::getValue($attributes, 'id');
            $password = Yii::$app->security->generateRandomString(8);
            $user->setPassword($password);
            if ($user->save()) {
                $profileData = [];
                if ($client->getName() === 'facebook') {
                    $profileData['firstname'] = ArrayHelper::getValue($attributes, 'first_name');
                    $profileData['lastname'] = ArrayHelper::getValue($attributes, 'last_name');
                }
                $user->afterSignup($profileData);
                $sentSuccess = Yii::$app->commandBus->handle(new SendEmailCommand([
                    'view' => 'oauth_welcome',
                    'params' => ['user' => $user, 'password' => $password],
                    'subject' => Yii::t('frontend', '{app-name} | Your login information', ['app-name' => Yii::$app->name]),
                    'to' => $user->email,
                ]));
                if ($sentSuccess) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                        'options' => ['class' => 'alert-success'],
                        'body' => Yii::t('frontend', 'Welcome to {app-name}. Email with your login information was sent to your email.', [
                            'app-name' => Yii::$app->name,
                        ]),
                        ]
                    );
                }
            } else {
                // We already have a user with this email. Do what you want in such case
                if ($user->email && User::find()->andWhere(['email' => $user->email])->notDeleted()->count()) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                        'options' => ['class' => 'alert-danger'],
                        'body' => Yii::t('frontend', 'We already have a user with email {email}', [
                            'email' => $user->email,
                        ]),
                        ]
                    );
                } else {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                        'options' => ['class' => 'alert-danger'],
                        'body' => Yii::t('frontend', 'Error while oauth process.'),
                        ]
                    );
                }
            }
        }
        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return true;
        } else {
            throw new Exception('OAuth error');
        }
    }
}
