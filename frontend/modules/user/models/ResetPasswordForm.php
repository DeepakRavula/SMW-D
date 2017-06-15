<?php

namespace frontend\modules\user\models;

use common\models\User;
use common\models\UserToken;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form.
 */
class ResetPasswordForm extends Model
{
    /**
     * @var
     */
    public $password;
    public $confirmPassword;

    /**
     * @var \common\models\UserToken
     */
    private $token;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array  $config name-value pairs that will be used to initialize the object properties
     *
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
        /* @var UserToken $tokenModel */
        $this->token = UserToken::find()
            ->notExpired()
            ->byType(UserToken::TYPE_PASSWORD_RESET)
            ->byToken($token)
            ->one();

        if (!$this->token) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['confirmPassword','password'], 'required'],
            [['confirmPassword','password'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => "Confirm Password doesn't match with the password"],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset
     */
    public function resetPassword()
    {
        $user = $this->token->user;
        $user->password = $this->password;
        if ($user->save()) {
            $this->token->delete();
        }

        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('frontend', 'Password'),
        ];
    }
}
