<?php

namespace common\models;

use Yii;

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
            [['userContactId', 'number'], 'required'],
            [['userContactId', 'extension'], 'integer'],
            [['number'], 'string', 'max' => 15],
			[['labelId'], 'safe']
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
	public function getUserContact()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId']);
    }
}