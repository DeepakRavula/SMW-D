<?php

namespace common\models;

use Yii;


/**
 * This is the model class for table "proforma_invoice".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $type
 * @property string $amount
 * @property string $date
 * @property int $status
 */
class ProformaLineItem extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_line_item';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id'], 'required'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice',
            
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
   
}
