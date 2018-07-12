<?php

namespace common\models;

use Yii;



class ProformaItemLesson extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma_item_lesson';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'enrolmentId'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson',
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
        return $this->hasOne(ProformaInvoice::className(), ['id' => 'proformaInvoiceId'])
            ->via('proformaLineItem');
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
   
    public function beforeSave($insert)
    {
        if ($insert) {
            if (!$this->enrolmentId) {
                $this->enrolmentId = $this->lesson->enrolment->id;
            }
        }
        return parent::beforeSave($insert);
    }
}
