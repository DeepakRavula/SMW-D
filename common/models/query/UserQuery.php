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
        $this->andWhere(['!=', 'user.status', User::STATUS_NOT_ACTIVE]);

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
        $this->joinWith('userLocation ul')
            ->joinWith(['qualifications' => function ($query) use ($programId) {
                $query->andWhere(['qualification.program_id' => $programId])
                    ->notDeleted();
            }])
            ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->where(['raa.item_name' => 'teacher'])
            ->andWhere(['ul.location_id' => $locationId]);

        return $this;
    }

    public function location($locationId)
    {
        $this->joinWith('userLocation')
            ->where(['location_id' => $locationId]);

        return $this;
    }
}
