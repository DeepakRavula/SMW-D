<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tax".
 *
 * @property integer $id
 * @property integer $province_id
 * @property double $tax_rate
 * @property string $since
 */
class Tax extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'status' => 'Status',
        ];
    } 
    
    public function getProvince()
    {
       return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }
}
