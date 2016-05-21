<?php

namespace common\models;

use Yii;
use common\models\query\InvoiceQuery;

/**
 * This is the model class for table "invoice".
 *
 * @property integer $id
 * @property integer $lesson_id
 * @property string $amount
 * @property string $date
 * @property integer $status
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lesson_id'], 'required'],
            [['lesson_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_id' => 'Lesson ID',
            'amount' => 'Amount',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @inheritdoc
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }
}
