<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class TeacherUnavailabilityQuery extends ActiveQuery
{
   public function overlap($teacherAvailability, $date)
   {
	   	$this->andWhere(['AND',
			[
				'<=', 'DATE(fromDate)', $date->format('Y-m-d')
			],
			[
				'>=', 'DATE(toDate)', $date->format('Y-m-d')
			]
		])
		->andWhere(['OR',  
			['AND', ['fromTime' => null],['toTime' => null]],
			['AND', 
				['>=', 'fromTime', $teacherAvailability->from_time], 
				['<=', 'toTime', $teacherAvailability->to_time]
			]
		]);
		return $this;
   }
}
