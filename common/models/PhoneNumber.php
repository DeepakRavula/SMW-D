<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "phone_number".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $label_id
 * @property string $number
 * @property integer $extension
 */
class PhoneNumber extends \yii\db\ActiveRecord
{
	const LABEL_HOME = 1;
	const LABEL_WORK = 2;
	const LABEL_OTHER = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'phone_number';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'label_id', 'number'], 'required'],
            [['user_id', 'label_id', 'extension'], 'integer'],
            [['number'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'label_id' => 'Label ID',
            'number' => 'Number',
            'extension' => 'Extension',
        ];
    }
}
