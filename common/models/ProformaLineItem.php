<?php

namespace common\models;

use Yii;
use common\models\ProformaItemInvoice;
use common\models\ProformaItemLesson;


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
    public $lessonId;
    public $invoiceId;

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
            [['proformaLineItemId'], 'safe'],


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
    public function afterSave($insert, $changedAttributes)
    {
        if($this->lessonId) {
            $proformaLessonItem=new ProformaItemLesson();
            $proformaLessonItem->lesson_id=$this->lessonId;
            $proformaLessonItem->proformaLineItemId=$this->id;
            $proformaLessonItem->save();
        }
        if($this->invoiceId) {
            $proformaInvoiceItem=new ProformaItemInvoice();
            $proformaInvoiceItem->invoice_id=$this->invoiceId;
            $proformaInvoiceItem->proformaLineItemId=$this->id;
            $proformaInvoiceItem->save();
        }
        

        return parent::afterSave($insert, $changedAttributes);
    }
    public function getProfromaInvoice()
    {
        return $this->hasOne(ProformaInvoice::className(), ['id' => 'invoice_id']);
    }
}
