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
        $this->andWhere(['!=', 'user.status', User::STATUS_DELETED]);
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

	public function teachers($programId, $locationId) {
		$this->joinWith(['userLocation ul' => function($query) use($programId){
				$query->joinWith('teacherAvailability');
			}])
			->joinWith(['qualification' => function($query) use($programId){
				$query->joinWith(['program' => function($query) use($programId){
					$query->where(['program.id' => $programId]);
				}]);
			}])
			->join('INNER JOIN','rbac_auth_assignment raa','raa.user_id = user.id')
			->where(['raa.item_name' => 'teacher'])
			->andWhere(['ul.location_id' => $locationId ]);
		return $this;
	}
}
