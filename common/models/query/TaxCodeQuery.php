<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\TaxCode]].
 *
 * @see \common\models\TaxCode
 */
class TaxCodeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\TaxCode[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\TaxCode|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
