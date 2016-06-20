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
            [['province_id', 'tax_rate'], 'required'],
            [['tax_rate'], 'number'],
            [['since'], 'safe'],
            [['since'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province_id' => 'Province Name',
            'tax_rate' => 'Tax Rate',
            'since' => 'Since',
        ];
    } 
    public function beforeSave($insert) {
        $sinceDate = \DateTime::createFromFormat('m-d-Y', $this->since);
        $this->since = $sinceDate->format('Y-m-d');

		return parent::beforeSave($insert);
	}
    public function getProvince()
    {
       return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }
}
