<?php

namespace backend\models;

use cheatsheet\Time;
use common\models\User;
use Yii;
use common\models\Location;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

/**
 * Login form.
 */
class LoginForm extends Model
{
    const SCENARIO_UNLOCK = 'unlock';

    public $pin;
    public $username;
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
            [['username', 'password'], 'required', 'except' => self::SCENARIO_UNLOCK],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword', 'except' => self::SCENARIO_UNLOCK],
            ['username', 'validateUser', 'except' => self::SCENARIO_UNLOCK],
            [['pin'], 'required', 'on' => self::SCENARIO_UNLOCK],
            [['pin'], 'validatePin', 'on' => self::SCENARIO_UNLOCK],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'password' => Yii::t('backend', 'Password'),
            'rememberMe' => Yii::t('backend', 'Remember Me'),
        ];
    }

    public function validateUser()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                $roles = Yii::$app->authManager->getRolesByUser($user->id);
                $role = end($roles);
                if (!in_array($role->name, [User::ROLE_STAFFMEMBER, User::ROLE_OWNER, User::ROLE_ADMINISTRATOR])) {
                    $this->addError('username', Yii::t('backend', 'You are not allowed to login.'));
                }
            } else {
                $this->addError('username', Yii::t('backend', 'Incorrect username or password.'));
            }
        }
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                if (!$user->password_hash || !$user->validatePassword($this->password)) {
                    $this->addError('password', Yii::t('backend', 'Incorrect username or password.'));
                }
            }
        }
    }
    
    public function validatePin()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserLocked();
            if (!$user) {
                $this->addError('pin', Yii::t('backend', 'Incorrect pin.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     *
     * @throws ForbiddenHttpException
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        $duration = $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0;
        if (Yii::$app->user->login($this->getUser(), $duration)) {
            if (!Yii::$app->user->can('loginToBackend')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException();
            }

            return true;
        }

        return false;
    }
    
    public function unlock()
    {
        if (!$this->validate()) {
            return false;
        }
        $duration = $this->rememberMe ? 28800 : 0;
        if (Yii::$app->user->login($this->getUserLocked(), $duration)) {
            if (!Yii::$app->user->can('loginToBackend')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException();
            }

            return true;
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
            $userName = $this->username;
            $this->user = User::find()
                ->backendUsers()
                ->canLogin()
                ->joinWith(['userContacts' => function ($query) use ($userName) {
                    $query->joinWith(['email' => function ($query) use ($userName) {
                        $query->andWhere(['email' => $userName]);
                    }])
                    ->primary();
                }])
                ->notDeleted()
                ->one();
        }

        return $this->user;
    }
    
    public function getUserLocked()
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $pin = md5($this->pin);
        $this->user = User::find()
                    ->location($locationId)
                    ->andWhere(['pin_hash' => $pin])
                    ->notDeleted()
                    ->one();

        return $this->user;
    }
}
