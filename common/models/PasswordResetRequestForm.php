<?php

namespace common\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\UserToken;
use common\models\Location;
use Yii;
use common\models\User;
use yii\base\Model;

/**
 * Password reset request form.
 */
class PasswordResetRequestForm extends Model
{
    /**
     * @var user email
     */
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\UserEmail',
                'message' => 'There is no user with such primary email.',
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $userName = $this->email;
        $user = User::find()
            ->joinWith(['userContacts' => function ($query) use ($userName) {
                $query->joinWith(['email' => function ($query) use ($userName) {
                    $query->andWhere(['email' => $userName]);
                }])
                ->primary();
            }])
            ->notDeleted()
            ->one();

        if ($user) {
            if ($user->isAdmin()) {
                $location = Location::findOne(['id' => Location::DEFAULT_LOCATION]);
            } else {
                $location = Location::findOne(['id' => $user->userLocation->location_id]);
            }
            $token = UserToken::create($user->id, UserToken::TYPE_PASSWORD_RESET, Time::SECONDS_IN_A_DAY);
            if ($user->save()) {
                return Yii::$app->commandBus->handle(new SendEmailCommand([
                    'from' => $location->email,
                    'to' => $this->email,
                    'subject' => Yii::t('frontend', 'Password reset for {name}', ['name' => Yii::$app->name]),
                    'view' => 'passwordResetToken',
                    'params' => [
                        'user' => $user,
                        'token' => $token->token,
                    ],
                ]));
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'E-mail'),
        ];
    }
}
