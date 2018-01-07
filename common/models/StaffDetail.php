<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "staff_detail".
 *
 * @property integer $id
 * @property integer $pin
 * @property integer $userId
 */
class StaffDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'staff_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pin', 'userId'], 'required'],
            [['pin', 'userId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pin' => 'Pin',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\StaffDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\StaffDetailQuery(get_called_class());
    }
}
