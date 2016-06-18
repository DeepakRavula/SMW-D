<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tax".
 *
 * @property string $id
 * @property string $province_id
 * @property double $tax_rate
 * @property string $from_date
 * @property string $to_date
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
            [['province_id', 'tax_rate'], 'required'],
            [['province_id'], 'integer'],
            [['tax_rate'], 'number'],
            [['from_date', 'to_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province_id' => 'Province ID',
            'tax_rate' => 'Tax Rate',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
        ];
    }
}
