<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\StaffDetail]].
 *
 * @see \common\models\StaffDetail
 */
class UserPinQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\StaffDetail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\StaffDetail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function location($locationId)
    {
        return $this->joinWith('userLocation')
            ->andWhere(['user_location.location_id' => $locationId]);
    }
    
    public function staffs()
    {
        return $this->joinWith(['user' => function($query) {
            $query->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                ->andWhere(['raa.item_name' => 'staffmember']);
        }]);
    }
}
