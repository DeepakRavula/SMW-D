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
class TaxType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','compounded', 'sort_order'], 'required'],
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
    
	public function getTaxCode()
    {
       return $this->hasMany(TaxCode::className(), ['tax_id' => 'id']);
    }
}
