<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserContact]].
 *
 * @see \common\models\UserContact
 */
class UserContactQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\UserContact[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\UserContact|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function primary()
    {
        return $this->andWhere(['user_contact.isPrimary' => true]);
    }
    
    public function location($locationId)
    {
        return $this->joinWith('userLocation')
            ->andWhere(['location_id' => $locationId]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['user_contact.isDeleted' => false]);
    }
}
