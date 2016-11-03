<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\TaxStatus]].
 *
 * @see \common\models\TaxStatus
 */
class TaxStatusQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\TaxStatus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\TaxStatus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
