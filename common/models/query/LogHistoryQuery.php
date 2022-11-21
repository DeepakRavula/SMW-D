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
    public function student($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_STUDENT, 'instanceId' => $id]);
    }

    public function course($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_COURSE, 'instanceId' => $id]);
    }
    public function lesson($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_LESSON, 'instanceId' => $id]);
    }
    public function enrolment($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_ENROLMENT, 'instanceId' => $id]);
    }
    public function invoice($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_INVOICE, 'instanceId' => $id]);
    }
    public function user($id)
    {
        return $this->andWhere(['instanceType' => LogObject::TYPE_USER, 'instanceId' => $id]);
    }
    public function location($locationId)
    {  
	    return $this->andWhere(['locationId' => $locationId]);
    }
    public function today() 
    {	
        return $this->andWhere(['>=', 'createdOn', (new \DateTime())->format('Y-m-d')]);	
    }
}
