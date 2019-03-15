<?php

namespace common\models\query;

use common\models\Payment;
use common\models\PaymentMethod;
use common\models\Invoice;
use yii\db\ActiveQuery;

/**
 * Class PaymentQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class PaymentQuery extends ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Payment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    /**
     * @return $this
     */
    public function proFormaInvoice()
    {
        $this->joinWith(['invoicePayment ip' => function ($query) {
            $query->joinWith(['invoice i' => function ($query) {
                $query->andWhere(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
            }]);
        }]);

        return $this;
    }

    public function customer($customerId)
    {
        return $this->andWhere(['payment.user_id' => $customerId]);
    }

    public function invoice()
    {
        return $this->joinWith(['invoicePayment ip' => function ($query) {
            $query->joinWith(['invoice i' => function ($query) {
                $query->andWhere(['i.type' => Invoice::TYPE_INVOICE]);
            }]);
        }]);
    }

    public function location($locationId)
    {
        return $this->joinWith(['user' => function ($query) use ($locationId) {
            $query->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->andWhere(['user_location.location_id' => $locationId]);
            }]);
        }]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['payment.isDeleted' => false]);
    }

    public function deleted()
    {
        return $this->andWhere(['payment.isDeleted' => true]);
    }

    public function exceptAutoPayments()
    {
        return $this->andWhere(['NOT', ['payment.payment_method_id' => [
            PaymentMethod::TYPE_CREDIT_USED, PaymentMethod::TYPE_CREDIT_APPLIED
        ]]]);
    }
    
    public function exceptGiftCard()
    {
        return $this->andWhere(['NOT', ['payment.payment_method_id' => [PaymentMethod::TYPE_GIFT_CARD ]]]);
    }

    public function giftCardPayments()
    {
        return $this->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_GIFT_CARD]);
    }
    
    public function creditUsed()
    {
        return $this->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]);
    }

    public function openingBalance()
    {
        return $this->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_ACCOUNT_ENTRY]);
    }

    public function notLessonCreditUsed()
    {
        return $this->joinWith(['debitPayment dp' => function ($query) {
            $query->joinWith(['lessonCredit' => function ($query) {
                $query->andWhere(['lesson_payment.id' => null]);
            }])
            ->andWhere(['OR', ['dp.id' => null], ['NOT', ['dp.id' => null]]]);
        }]);
    }

    public function lessonCreditUsed()
    {
        return $this->joinWith(['debitPayment dp' => function ($query) {
            $query->joinWith(['lessonCredit' => function ($query) {
                $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
            }])
            ->andWhere(['NOT', ['dp.id' => null]])
            ->andWhere(['dp.isDeleted' => false]);
        }]);
    }
    
    public function notCreditUsed()
    {
        return $this->andWhere(['NOT', ['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]]);
    }
    
    public function creditApplied()
    {
        return $this->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_APPLIED]);
    }

    public function credit()
    {
        return $this->andWhere(['>', 'payment.balance', 0.0]);
    }
}
