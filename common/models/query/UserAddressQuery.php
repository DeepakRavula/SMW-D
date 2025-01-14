<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserContact]].
 *
 * @see \common\models\UserContact
 */
class UserAddressQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\UserAddress[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\UserAddress|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['user_address.isDeleted' => false]);
    }
}
