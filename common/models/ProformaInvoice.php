<?php

namespace common\models;

use Yii;
use common\models\query\ProformaInvoiceQuery;

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
class ProformaInvoice extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_invoice';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId','locationId'], 'required'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'date' => 'Date',
            'userId' => 'Customer Name',
            'locationId' =>'location',
            
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
    public static function find()
    {
        return new ProformaInvoiceQuery(get_called_class());
    }

}
