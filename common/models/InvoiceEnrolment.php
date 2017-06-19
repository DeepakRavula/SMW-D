<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_enrolment".
 *
 * @property string $id
 * @property string $invoiceId
 * @property string $enrolemntId
 */
class InvoiceEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'enrolemntId'], 'required'],
            [['invoiceId', 'enrolemntId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'enrolemntId' => 'Enrolemnt ID',
        ];
    }
}
