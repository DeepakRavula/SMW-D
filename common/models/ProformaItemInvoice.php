<?php

namespace common\models;

use Yii;



class ProformaItemInvoice extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_item_invoice';
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
    public function getProformaLineItem()
    {
        return $this->hasOne(ProformaLineItem::className(), ['id' => 'proformaLineItemId']);
    }
    public function getProformaInvoice()
    {
        return $this->hasOne(ProformaInvoice::className(), ['id' => 'invoice_id'])
        ->via('proformaLineItem');
    }
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
}
