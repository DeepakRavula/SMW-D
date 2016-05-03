<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "program".
 *
 * @property integer $id
 * @property string $name
 * @property integer $rate
 * @property integer $status
 */
class Program extends \yii\db\ActiveRecord
{
    const STATUS_IN_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%program}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['rate', 'status'], 'integer'],
            [['name'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'rate' => 'Rate',
            'status' => 'Status',
        ];
    }
    
    /**
     * Returns program statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_IN_ACTIVE => Yii::t('common', 'In Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active')
        ];
    }
}
