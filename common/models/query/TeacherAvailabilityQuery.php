<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\TeacherAvailability]].
 *
 * @see \common\models\TeacherAvailability
 */
class TeacherAvailabilityQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\TeacherAvailability[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\TeacherAvailability|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['AND', ['between', $fromDate->format('H:i:s'), 'from_time',
            'to_time'],
            ['between', $toDate->format('H:i:s'), 'from_time',
            'to_time']]);
    }
}
