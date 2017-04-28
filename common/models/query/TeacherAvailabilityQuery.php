<?php

namespace common\models\query;

use common\models\TeacherAvailability;
use yii\db\ActiveQuery;

/**
 * Class UserQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class TeacherAvailabilityQuery extends ActiveQuery
{
  public function teacher($teacherId)
  {
	$this->joinWith(['teacher' => function ($query) use($teacherId){
		$query->where(['user.id' => $teacherId]);
	}]);
	return $this;
  }
  public function day($day)
  {
	$this->andWhere(['teacher_availability_day.day' => $day]);
	return $this;
  }
  public function time($fromTime, $toTime)
  {
	$this->andWhere(['AND',
		[
			'<=', 'from_time', $fromTime
		],
		[
			'>=', 'to_time', $toTime
		]
    ]);
	return $this;
  }
  
    public function between($fromTime, $toTime)
    {
        $this->andWhere(['OR', 
            [
                'between', 'from_time', (new \DateTime($fromTime))->format('H:i:s'),
                (new \DateTime($toTime))->format('H:i:s')
            ],
            [
                'between', 'DATE_SUB(to_time, INTERVAL 1 SECOND)', (new \DateTime($fromTime))->format('H:i:s'),
                (new \DateTime($toTime))->format('H:i:s')
            ],
            [
                'AND',
                [
                    '<=', 'from_time', (new \DateTime($fromTime))->format('H:i:s')
                ],
                [
                    '>=', 'DATE_SUB(to_time, INTERVAL 1 SECOND)', (new \DateTime($toTime))->format('H:i:s')
                ]

            ]
        ]);
        return $this;
    }
}
