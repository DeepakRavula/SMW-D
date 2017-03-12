<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/5/14
 * Time: 10:46 AM.
 */

namespace common\models\query;

use yii\db\ActiveQuery;

class TimelineEventQuery extends ActiveQuery
{
    public function today()
    {
        $this->andWhere(['>=', 'created_at', strtotime('today midnight')]);

        return $this;
    }

	public function lesson()
	{
		$this->andWhere(['category' => 'lesson']);
		return $this;
	}

	public function enrolment()
	{
		$this->andWhere(['category' => 'enrolment']);
		return $this;
	}

	public function user()
	{
		$this->andWhere(['category' => 'user']);
		return $this;
	}

	public function payment()
	{
		$this->andWhere(['category' => 'payment']);
		return $this;
	}
}
