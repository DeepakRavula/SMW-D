<?php

namespace common\models;

use Yii;
use common\models\ProformaItemInvoice;
use common\models\ProformaItemLesson;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\query\ProformaLineItemQuery;


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
    public $enrolmentId;

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
            [['proformaInvoiceId'], 'required'],
            [['proformaLineItemId','lessonId','invoiceId', 'enrolmentId',
                'isDeleted'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proformaInvoiceId' => 'Invoice',
        ];
    }

    public static function find()
    {
        return new ProformaLineItemQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->lessonId) {
                $proformaLessonItem = new ProformaItemLesson();
                $proformaLessonItem->lessonId = $this->lessonId;
                $proformaLessonItem->enrolmentId = $this->enrolmentId;
                $proformaLessonItem->proformaLineItemId = $this->id;
                $proformaLessonItem->save();
            }
            if ($this->invoiceId) {
                $proformaInvoiceItem = new ProformaItemInvoice();
                $proformaInvoiceItem->invoiceId = $this->invoiceId;
                $proformaInvoiceItem->proformaLineItemId = $this->id;
                $proformaInvoiceItem->save();
            }
        } else {
            if ($this->lessonLineItem) {
                if ($this->lesson->isPrivate()) {
                    $enrolmentId = $this->lesson->enrolment->id;
                } else {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $this->lesson->courseId])
                        ->customer($this->proformaInvoice->userId)
                        ->one();
                    $enrolmentId = $enrolment->id;
                }
                if (!$this->lesson->isOwing($enrolmentId)) {
                    $this->delete();
                }
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getProformaInvoice()
    {
        return $this->hasOne(ProformaInvoice::className(), ['id' => 'proformaInvoiceId']);
    }
    
    public function getLessonLineItem()
    {
        return $this->hasOne(ProformaItemLesson::className(), ['proformaLineItemId' => 'id']);
    }
    
    public function getInvoiceLineItem()
    {
        return $this->hasMany(ProformaItemInvoice::className(), ['proformaLineItemId' => 'id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
            ->via('lessonLineItem');
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId'])
            ->via('invoiceLineItem');
    }
}
