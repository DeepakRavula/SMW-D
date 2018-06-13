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
    public $lessonIds;
    public $invoiceIds;
    public $lessonId;
    public $fromDate;
    public $toDate;
    public $dateRange;
    
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
            [['lessonIds', 'invoiceIds', 'dateRange', 'fromDate', 'toDate', 'lessonId'], 'safe']
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->date = (new \DateTime())->format('Y-m-d');
        }
        return parent::beforeSave($insert);
    }
}
