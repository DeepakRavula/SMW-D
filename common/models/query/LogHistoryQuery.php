<?php
namespace common\models\query;
use yii\db\ActiveQuery;
use common\models\log\LogObject;
/**
 * Class UserQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LogHistoryQuery extends ActiveQuery
{
	public function student($id) {
		return	$this->andWhere(['instanceType' => LogObject::STUDENT, 'instanceId' => $id]);
	}
    public function course($id)  {
        return $this->andWhere(['instanceType' => LogObject::COURSE, 'instanceId' => $id]);
    }
}