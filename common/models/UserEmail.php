<?php

namespace common\models;

use Yii;
use common\models\query\UserEmailQuery;

/**
 * This is the model class for table "user_email".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $email
 * @property string $labelId
 * @property integer $isPrimary
 */
class UserEmail extends \yii\db\ActiveRecord
{
    const SCENARIO_USER_CREATE = 'user-create';

    private $labelId;

    public function getLabelId()
    {
        return $this->labelId;
    }

    public function setLabelId($value)
    {
        $this->labelId = trim($value);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userContactId'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['labelId'], 'safe'],
            [['email'], 'trim'],
            ['email', 'required', 'on' => self::SCENARIO_USER_CREATE],
            ['email', 'unique', 'targetClass'=> self::className(), 'filter' => function ($query) {
                $query->joinWith(['userContact uc' => function ($query) {
                    $query->joinWith(['user u' => function ($query) {
                        $query->andWhere(['u.isDeleted' => false]);
                    }]);
                }]);
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['user_email.id' => $this->id]]);
                } 
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'email' => 'Email',
            'labelId' => 'Label',
            'isPrimary' => 'Is Primary',
        ];
    }
    
    public static function find()
    {
        return new UserEmailQuery(get_called_class());
    }
    
    public function getUserContact()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId'])
                ->via('userContact');
    }
    
    public function beforeDelete() 
    {
        foreach ($this->user->emails as $email) {
            if ($this->id !== $email->id) {
                $email->makePrimary();
                break;
            }
        }
        if ($this->userContact) {
            $this->userContact->delete();
        }
        return parent::beforeDelete();
    }
    
    public function afterSave($insert, $changedAttributes) 
    {
        if (!$this->user->hasPrimaryEmail()) {
            $this->makePrimary();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function makePrimary()
    {
        return $this->userContact->makePrimary();
    }
}
