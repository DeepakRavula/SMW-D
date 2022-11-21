<?php

namespace common\models;
use Yii;
/**
 * This is the model class for table "tax_code".
 *
 * @property string $id
 * @property string $tax_id
 * @property string $province_id
 * @property string $rate
 * @property string $start_date
 */
class TaxCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tax_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tax_type_id', 'province_id', 'rate', 'code'], 'required'],
            [['tax_type_id'], 'integer'],
            [['rate'], 'number'],
            [['start_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_type_id' => 'Tax Name',
            'province_id' => 'Province Name',
            'rate' => 'Rate',
            'start_date' => 'Start Date',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\TaxCodeQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\TaxCodeQuery(get_called_class());
    }

    public function getTaxType()
    {
        return $this->hasOne(TaxType::className(), ['id' => 'tax_type_id']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }

    public function getTaxStatus()
    {
        return $this->hasOne(TaxStatus::className(), ['id' => 'tax_status_id'])
        ->viaTable('tax_type_tax_status_assoc', ['tax_type_id' => 'tax_type_id']);
    }
    public function beforeSave($insert)
    {
        $startDate = Yii::$app->formatter->asDate($this->start_date);
        $this->start_date = (new \DateTime($startDate))->format('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
