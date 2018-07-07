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
    public function paid()
    {
        $this->joinWith('payment p');

        return $this;
    }
}
