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
        $this->joinWith(['teacher' => function ($query) use ($teacherId) {
            $query->andWhere(['user.id' => $teacherId]);
        }]);
        return $this;
    }

    public function location($locationId)
    {
        return $this->joinWith(['userLocation' => function ($query) use ($locationId) {
            $query->andWhere(['user_location.location_id' => $locationId]);
        }]);
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
  
    public function overlap($fromTime, $toTime)
    {
        return $this->andWhere(['OR',
            [
                'between', 'from_time', (new \DateTime($fromTime))->format('H:i:s'),
                (new \DateTime($toTime))->format('H:i:s')
            ],
            [
                'between', 'to_time', (new \DateTime($fromTime))->format('H:i:s'),
                (new \DateTime($toTime))->format('H:i:s')
            ],
            [
                'AND',
                [
                    '<=', 'from_time', (new \DateTime($fromTime))->format('H:i:s')
                ],
                [
                    '>=', 'to_time', (new \DateTime($toTime))->format('H:i:s')
                ]

            ]
        ]);
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
    public function qualification($locationId, $programId)
    {
        $this->joinWith(['userLocation' => function ($query) use ($locationId, $programId) {
            $query->andWhere(['user_location.location_id' => $locationId]);
            $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                $query->andWhere(['qualification.program_id' => $programId]);
            }]);
        }]);
        return $this;
    }

    public function notDeleted() 
    {
        return $this->andWhere(['teacher_availability_day.isDeleted' => false]);
    }
}
