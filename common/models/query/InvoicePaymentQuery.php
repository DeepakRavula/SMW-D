<?php

namespace common\models\query;

class InvoicePaymentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Allocation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Allocation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['invoice_payment.isDeleted' => false]);
    }

    public function notLessonCreditUsed()
    {
        return $this->joinWith(['debitPayment dp' => function ($query) {
            $query->joinWith(['lessonCredit' => function ($query) {
                $query->andWhere(['lesson_payment.id' => null]);
            }])
            ->where(['OR', ['dp.id' => null], ['NOT', ['dp.id' => null]]]);
        }]);
    }

    public function invoice($invoiceId)
    {
        return $this->andWhere(['invoice_payment.invoice_id' => $invoiceId]);
    }
}
