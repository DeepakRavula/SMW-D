<?php

namespace common\models\query;

use common\models\User;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', User::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

	public function teachers() {
		return $this->byRole(User::ROLE_TEACHER);
	}

    /**
     * @return $this
     */
    public function byRole($ole)
    {

 		$this->leftJoin(['rbac_auth_assignment aa'], 'u.id = aa.user_id')
 			->leftJoin(['rbac_auth_item ai'], 'aa.item_name = ai.name')
            ->andFilterWhere(['like', 'location_id', $locationId])
            ->andFilterWhere(['ai.name' => $role]);
        return $this;
    }
}