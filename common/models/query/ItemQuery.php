<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Item]].
 *
 * @see \common\models\Item
 */
class ItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\Item[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Item|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function location($locationId)
    {
        return $this->andFilterWhere(['item.locationId' => $locationId]);
    }
}
