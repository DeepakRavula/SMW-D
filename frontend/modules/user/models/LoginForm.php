<?php

namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form.
 */
class LoginForm extends Model
{
    public $identity;
    public $password;
    public $rememberMe = true;

    private $user = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['identity', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['identity', 'validateUser'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'identity' => Yii::t('frontend', 'Username or email'),
            'password' => Yii::t('frontend', 'Password'),
            'rememberMe' => Yii::t('frontend', 'Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        $misMatchCount = 0;
        $userCount = count($this->getUsers());
        if (!$this->hasErrors()) {
            $users = $this->getUsers();
            foreach ($users as $user) {
                if (!$user->password_hash || !$user->validatePassword($this->password)) {
                    $misMatchCount += 1;
                }
            }
        }
        if ($misMatchCount == $userCount) {
            $this->addError('password', Yii::t('frontend', 'Incorrect username or password.'));
        }
    }

    public function validateUser()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $roles = Yii::$app->authManager->getRolesByUser($user->id);
            $role = end($roles);
            if (!$user || !in_array($role->name, [User::ROLE_TEACHER, User::ROLE_CUSTOMER])) {
                $this->addError('identity', Yii::t('frontend', 'You are not allowed to login.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getUser(), $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Finds user by [[username]].
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $userName = $this->identity;
            $this->user = User::find()
                ->notDeleted()
                ->joinWith(['userContacts' => function ($query) use ($userName) {
                    $query->joinWith(['email' => function ($query) use ($userName) {
                        $query->notDeleted()
                            ->andWhere(['email' => $userName]);
                    }])
                    ->primary()
                    ->notDeleted();
                }])
                ->one();
        }

        return $this->user;
    }

    public function getUsers()
    {
        $userName = $this->identity;
        $users = User::find()
            ->notDeleted()
            ->joinWith(['userContacts' => function ($query) use ($userName) {
                $query->joinWith(['email' => function ($query) use ($userName) {
                    $query->notDeleted()
                        ->andWhere(['email' => $userName]);
                }])
                ->primary()
                ->notDeleted();
            }])
            ->all();

        return $users;
    }
}
