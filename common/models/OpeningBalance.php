<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\PaymentQuery;
use common\models\PaymentMethod;
use Carbon\Carbon;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property int $payment_method_id
 * @property float $amount
 */
class OpeningBalance extends ActiveRecord
{
    public $isCredit;
    public $amount;
    public $user_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
	    ['amount','required'],
            [['amount'], 'number', 'min' => 0.1],
	    [['amount'],'number','numberPattern' => '/^\d+(.\d{1,2})?$/', 'message' => 'Only 2 decimal spaces allowed.'],
            [['isCredit', 'user_id'], 'required']
        ];
    }

    public function addOpeningBalance()
    {
        $payment = new Payment();
        $payment->user_id = $this->user_id;
        $payment->payment_method_id = PaymentMethod::TYPE_ACCOUNT_ENTRY;
        if ($this->isCredit) {
            $this->amount *= -1;
        }
        $payment->amount = $this->amount;
        $payment->save();
        return $payment;
    }
}
