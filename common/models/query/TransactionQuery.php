<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Transaction]].
 *
 * @see \common\models\Transaction
 */
class TransactionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\Transaction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Transaction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function manualPayments($userId)
    {
        return $this->joinWith(['payment' => function ($query) use ($userId) {
            $query->joinWith(['customerCredit' => function ($query) use ($userId) {
                $query->andWhere(['NOT',['customer_payment.id' => null]])
                ->andWhere(['customer_payment.userId' => $userId]);
            }])
            ->notDeleted()
            ->exceptAutoPayments();
        }]);
    }

    public function invoices($userId)
    {
        return $this->joinWith(['invoice' => function ($query) use ($userId) {
            $query->notDeleted()
                ->invoice()
                ->customer($userId);
        }]);
    }
}
