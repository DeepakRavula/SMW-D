<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tax_taxstatus".
 *
 * @property string $int
 * @property string $tax_id
 * @property string $tax_status_id
 * @property integer $exempt
 */
class TaxTaxstatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_taxstatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tax_id', 'tax_status_id', 'exempt'], 'required'],
            [['tax_id', 'tax_status_id', 'exempt'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'int' => 'Int',
            'tax_id' => 'Tax ID',
            'tax_status_id' => 'Tax Status ID',
            'exempt' => 'Exempt',
        ];
    }
}
