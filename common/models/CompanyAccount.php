<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company_account_info".
 *
 * @property string $description
 * @property integer $invoiceId
 * @property string $date
 * @property string $debit
 * @property string $credit
 */
class CompanyAccount extends \yii\db\ActiveRecord
{
    const INVOICE_DESCRIPTION = 'Invoice';
    const PAYMENT_DESCRIPTION = 'Payment';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_account_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'transactionId'], 'integer'],
            [['date'], 'safe'],
            [['description'], 'string', 'max' => 7],
            [['debit'], 'string', 'max' => 12],
            [['credit'], 'string', 'max' => 13],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description' => 'Description',
            'invoiceId' => 'Invoice ID',
            'date' => 'Date',
            'debit' => 'Debit',
            'credit' => 'Credit',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CustomerAccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CompanyAccountQuery(get_called_class());
    }
    
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId']);
    }
    
    public function getBalance()
    {
        return self::find()
                ->andWhere(['userId' => $this->userId])
                ->andWhere(['<=', 'transactionId', $this->transactionId])
                ->sum('credit+debit');
    }
    
    public function getAccountDescription()
    {
        $description = null;
        switch ($this->description) {
            case self::INVOICE_DESCRIPTION:
                $description = $this->description . ' I-' . str_pad($this->invoiceId, 5, 0, STR_PAD_LEFT);
            break;
            case self::PAYMENT_DESCRIPTION:
                if (!$this->invoice->isInvoice()) {
                    $description = $this->description . ' P-' . str_pad($this->invoiceId, 5, 0, STR_PAD_LEFT);
                } else {
                    $description = $this->description . ' I-' . str_pad($this->invoiceId, 5, 0, STR_PAD_LEFT);
                }
            break;
        }
        return $description;
    }
}
