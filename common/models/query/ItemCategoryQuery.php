<?php

namespace common\models\query;

use common\models\ItemCategory;

/**
 * This is the ActiveQuery class for [[\common\models\ItemCategory]].
 *
 * @see \common\models\ItemCategory
 */
class ItemCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ItemCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ItemCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->andFilterWhere(['NOT', ['name' => [ItemCategory::LESSON_ITEM,
             ItemCategory::OPENING_BALANCE_ITEM]]]);
    }

    public function notDeleted()
    {
        return $this->andFilterWhere(['isDeleted' => false]);
    }
}
