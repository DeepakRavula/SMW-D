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
                if ($programId) {
                    $query->andWhere(['qualification.program_id' => $programId])
                        ->notDeleted();
                }
            }])
            ->allTeachers();

        return $this;
    }

    public function teachersInLocation($locationId)
    {
        return $this->joinWith(['userLocation ul' => function ($query) use ($locationId) {
                $query->andWhere(['ul.location_id' => $locationId]);
            }])
            ->allTeachers();
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
        return $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['OR', ['raa.item_name' => ['owner', 'administrator']]]);
    }

    public function admin()
    {
        return $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'administrator']);
    }
    
    public function backendUsers()
    {
        return $this->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['OR', ['raa.item_name' => ['owner', 'administrator', 'staffmember']]]);
    }
    public function adminWithLocation($locationId)
    {
        return $this->joinWith('userLocation')
            ->join('INNER JOIN', 'rbac_auth_assignment raa_new', 'raa_new.user_id = user.id')
            ->andWhere(['OR', ['raa_new.item_name' => 'administrator'], ['location_id' => $locationId]]);
    }
    
    public function canLogin()
    {
        return $this->andWhere(['user.canLogin' => true]);
    }

    public function location($locationId)
    {
        $this->joinWith('userLocation uslo')
            ->andWhere(['uslo.location_id' => $locationId]);

        return $this;
    }
}
