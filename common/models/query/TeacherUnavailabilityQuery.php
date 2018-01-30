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
        ]);
        return $this;
    }
}
