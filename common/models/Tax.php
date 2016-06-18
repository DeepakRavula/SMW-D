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
            [['province_id'], 'integer'],
            [['tax_rate'], 'number'],
            [['since'], 'required'],
            [['since'], 'safe'],
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
            'since' => 'Since',
        ];
    }
}