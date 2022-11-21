<?php

namespace common\models\query;

use common\models\TeacherRoom;

/**
 * This is the ActiveQuery class for [[\common\models\TeacherRoom]].
 *
 * @see \common\models\TeacherRoom
 */
class TeacherRoomQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\TeacherRoom[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\TeacherRoom|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function location($locationId)
    {
        $this->joinWith(['teacherAvailability' => function ($query) use ($locationId) {
            $query->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->andWhere(['location_id' => $locationId]);
            }]);
        }]);

        return $this;
    }

    public function day($day)
    {
        $this->joinWith(['teacherAvailability' => function ($query) use ($day) {
            $query->andWhere(['day' => $day])
                ->notDeleted();
        }]);

        return $this;
    }

    public function between($fromTime, $toTime)
    {
        $this->joinWith(['teacherAvailability' => function ($query) use ($fromTime, $toTime) {
            $query->andWhere(['OR',
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
        }]);

        return $this;
    }
}
