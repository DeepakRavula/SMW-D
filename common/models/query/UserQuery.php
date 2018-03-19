<?php

namespace common\models\query;

use common\models\User;
use yii\db\ActiveQuery;

/**
 * Class UserQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['user.isDeleted' => false]);

        return $this;
    }
    public function notDraft()
    {
        $this->andWhere(['NOT IN', 'user.status', User::STATUS_DRAFT]);

        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['user.status' => User::STATUS_ACTIVE]);

        return $this;
    }

    public function teachers($programId, $locationId)
    {
        $this->joinWith(['userLocation ul' => function ($query) use ($locationId) {
                $query->andWhere(['ul.location_id' => $locationId]);
            }])
            ->joinWith(['qualifications' => function ($query) use ($programId) {
                $query->andWhere(['qualification.program_id' => $programId])
                    ->notDeleted();
            }])
            ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'teacher']);

        return $this;
    }

    public function customers($locationId)
    {
        $this->joinWith('userLocation ul')
            ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'customer'])
            ->andWhere(['ul.location_id' => $locationId]);

        return $this;
    }
    
    public function staffs()
    {
        $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'staffmember']);

        return $this;
    }
    
    public function allTeachers()
    {
        return $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'teacher']);
    }
    
    public function adminOrOwner()
    {
        $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['OR', ['raa.item_name' => ['owner', 'administrator']]]);

        return $this;
    }
    
    public function backendUsers()
    {
        return $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['OR', ['raa.item_name' => ['owner', 'administrator', 'staffmember']]]);
    }
    
    public function canLogin()
    {
        return $this->andWhere(['user.canLogin' => true]);
    }

    public function location($locationId)
    {
        $this->joinWith('userLocation')
            ->andWhere(['location_id' => $locationId]);

        return $this;
    }
}
