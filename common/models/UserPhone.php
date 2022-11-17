<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "user_phone".
 *
 * @property integer $id
 * @property integer $userContactId
 * @property string $number
 * @property integer $extension
 */
class UserPhone extends \yii\db\ActiveRecord
{
    private $labelId;

    const CONSOLE_USER_ID  = 727;

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
        return 'user_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number'], 'required'],
            [['userContactId', 'extension'], 'integer'],
            [['number'], 'string', 'max' => 15],
            [['labelId', 'isDeleted', 'note'], 'safe']
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
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userContactId' => 'User Contact ID',
            'number' => 'Number',
            'extension' => 'Extension',
            'note' => 'Note'
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserPhoneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserPhoneQuery(get_called_class());
    }

    public function setModel($model)
    {
        $this->number = $model->number;
        $this->labelId = $model->phoneLabelId;
        $this->extension = $model->extension;
        return $this;
    }
    
    public function getUserContact()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId']);
    }
    
    public function beforeDelete() 
    {
        if ($this->userContact) {
            $this->userContact->delete();
        }
        return parent::beforeDelete();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
