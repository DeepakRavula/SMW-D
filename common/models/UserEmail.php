<?php

namespace common\models;

use Yii;

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
    
   
	public function getUserContact()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId']);
    }
}
