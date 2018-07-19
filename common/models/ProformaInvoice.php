<?php

namespace common\models;

use Yii;
use common\models\Location;
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
    public $lessonIds;
    public $invoiceIds;
    public $lessonId;
    public $fromDate;
    public $toDate;
    public $dateRange;

    const STATUS_UNPAID = 1;
    const STATUS_PAID = 2;
    
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
            [['userId', 'locationId'], 'required'],
            [['lessonIds', 'invoiceIds', 'dateRange', 'fromDate', 'toDate', 'lessonId','notes', 'status', 'dueDate'], 'safe']
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
            'notes'  =>'Message',
            'status' => 'Status',
            'dueDate' => 'Due Date',
            
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

    public function getTotal()
    {
        $lessonTotal = 0;
        $invoiceTotal = 0;
        $invoiceId = $this->id;
        $lessonLineItems = Lesson::find()
            ->joinWith(['proformaLessonItem' => function ($query) use ($invoiceId) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($invoiceId) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $invoiceId]);
                }]);
            }])
            ->all();
        foreach ($lessonLineItems as $lessonLineItem) {
            $lessonTotal += $lessonLineItem->netPrice;
        }
        $invoiceLineItems = Invoice::find()
            ->joinWith(['proformaInvoiceItem' => function ($query) use ($invoiceId) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($invoiceId){
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $invoiceId]);
                }]);
            }])
            ->all();
        foreach ($invoiceLineItems as $invoiceLineItem) {
            $invoiceTotal += $invoiceLineItem->balance;
        }
        return $lessonTotal + $invoiceTotal;
    }
    
    public function getProformaInvoiceNumber()
    {
        $proformaInvoiceNumber = str_pad($this->proforma_invoice_number, 5, 0, STR_PAD_LEFT);
            return 'PR-'.$proformaInvoiceNumber;
    }
    
    public function beforeSave($insert)
    {
        $lastInvoice   = $this->lastInvoice();
        if (!empty($lastInvoice)) {
            $proformaInvoiceNumber = $lastInvoice->proforma_invoice_number + 1;
        }
        else{
            $proformaInvoiceNumber=1;
        }
        $this->proforma_invoice_number = $proformaInvoiceNumber;
        if ($insert) {
            $this->date = (new \DateTime())->format('Y-m-d');
            $this->dueDate = (new \DateTime())->format('Y-m-d');
            $this->status = self::STATUS_UNPAID;
        }
        return parent::beforeSave($insert);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    public function getStatus()
    {
        $status = null;
        
        switch ($this->status) {
            case self::STATUS_UNPAID:
                $status = 'Unpaid';
            break;
            case self::STATUS_PAID:
                $status = 'Paid';
            break;
        }
        return $status;
    }
    public function lastInvoice()
    {
        return $query = ProformaInvoice::find()->alias('i')
                    ->andWhere(['i.locationId' => $this->locationId])
                    ->orderBy(['i.id' => SORT_DESC])
                    ->one();
    }
    
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }
    public function getTotalDiscount()
    {
        $discount = 0.0;
        $lineItems  =   $this->proformaLineItems;
        foreach($lineItems as $lineItem) 
        {
            if($lineItem->lessonLineItem) {
                $discount+=$lineItem->lesson->discount;
            }
            if($lineItem->invoiceLineItem){
                $discount+=$lineItem->invoice->totalDiscount;
            }
           
        }
        return $discount;
    }
    public function getSubtotal()
    {
        $subtotal = 0.0;
        $lineItems  =   $this->proformaLineItems;
         foreach($lineItems as $lineItem)
        {
            if($lineItem->lessonLineItem){
                $subtotal+=$lineItem->lesson->netPrice;
            }
            if($lineItem->invoiceLineItem){
                $subtotal+=$lineItem->invoice->subTotal;
            }

        }
        return $subtotal;
    }
    
    public function getProformaLineItems()
    {
        return $this->hasMany(ProformaLineItem::className(), ['proformaInvoiceId' => 'id']);
    }
}
