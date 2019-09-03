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
    public function overlap($date)
    {
        return $this->andWhere(['OR', 
            ['AND',
                [
                    '<', 'DATE(fromDateTime)', $date->format('Y-m-d')
                ],
                [
                    '>', 'DATE(toDateTime)', $date->format('Y-m-d')
                ]
            ], 
            [
                'DATE(fromDateTime)' => $date->format('Y-m-d')
            ],
            [
                'DATE(toDateTime)' => $date->format('Y-m-d')
            ]
        ]);
    }
}
