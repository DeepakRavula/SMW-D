<?php

namespace common\models;

use Yii;
use common\models\query\UserEmailQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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
            ['email', 'required', 'on' => self::SCENARIO_USER_CREATE],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['labelId', 'isDeleted'], 'safe'],
            [['email'], 'trim'],
            ['email', 'validateUnique'],
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
    
    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
        ];
    }

    public static function find()
    {
        return new UserEmailQuery(get_called_class());
    }
    
    public function validateUnique($attributes)
    {
        $query = self::find()
                ->andWhere(['email' => $this->email])
                ->joinWith(['userContact uc' => function ($query) {
                    $query->joinWith(['user u' => function ($query) {
                        $query->andWhere(['u.isDeleted' => false]);
                    }]);
                }]);
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['user_email.id' => $this->id]]);
                }
        $email = $query->one();
        if ($email) {
            return $this->addError($attributes, "Email already exists!");
        }
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

    public function setModel($model)
    {
        $this->email = $model->email;
        $this->labelId = $model->labelId;
        return $this;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
        $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
