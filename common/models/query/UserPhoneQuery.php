<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserPhone]].
 *
 * @see \common\models\UserPhone
 */
class UserPhoneQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\UserPhone[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\UserPhone|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
	
	public function notDeleted()
    {
        return $this->andWhere(['user_phone.isDeleted' => false]);
    }
}
