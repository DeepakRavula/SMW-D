<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property integer $id
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TransactionQuery(get_called_class());
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['transactionId' => 'id']);
    }

    public function getCustomerPayment()
    {
        return $this->hasOne(CustomerPayment::className(), ['paymentId' => 'id'])
            ->via('payment');
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['transactionId' => 'id']);
    }

    public function getUser()
    {
        if ($this->payment) {
            return $this->hasOne(User::className(), ['id' => 'userId'])
                ->via('customerPayment');
        } else {
            return $this->hasOne(User::className(), ['id' => 'user_id'])
                ->via('invoice');
        }
    }

    public function getDate()
    {
        return $this->invoice ? $this->invoice->date : $this->payment->date;
    }

    public function getCredit($isCustomerView)
    {
        if ($isCustomerView) {
            $credit = $this->invoice ? $this->invoice->total : null;
        } else {
            $credit = $this->payment ? $this->payment->amount : null;
        }
        return $credit;
    }

    public function getDebit($isCustomerView)
    {
        if ($isCustomerView) {
            $debit = $this->payment ? $this->payment->amount : null;
        } else {
            $debit = $this->invoice ? $this->invoice->total : null;
        }
        return $debit;
    }

    public function getBalance($isCustomerView)
    {
        $paymentQuery = Transaction::find()
                ->manualPayments($this->user->id);
        $invoiceQuery = Transaction::find()
                ->invoices($this->user->id)
                ->union($paymentQuery)
                ->all();
        $ids = ArrayHelper::getColumn($invoiceQuery, 'id');
        $accountQuery = Transaction::find()
            ->andWhere(['id' => $ids])
            ->andWhere(['<=', 'id',  $this->id])
            ->orderBy(['transaction.id' => SORT_DESC])
            ->all();
        $debit = 0;
        $credit = 0;
        foreach ($accountQuery as $transaction) {
            $debit += $transaction->getDebit($isCustomerView);
            $credit += $transaction->getCredit($isCustomerView);
        }
        return $debit - $credit;
    }

    public function getAccountDescription()
    {
        return $this->payment ? 'Payment' : $this->invoice->getInvoiceNumber();
    }
}
