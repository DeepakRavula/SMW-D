<?php

namespace common\models\query;

use common\models\LocationAvailability;

/**
 * This is the ActiveQuery class for [[\common\models\Lesson]].
 *
 * @see \common\models\Lesson
 */
class LocationAvailabilityQuery extends \yii\db\ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }
    
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function scheduleVisibilityHours()
    {
        return $this->andWhere(['location_availability.type' => LocationAvailability::TYPE_SCHEDULE_TIME]);
    }

    public function locationaAvailabilityHours()
    {
        return $this->andWhere(['location_availability.type' => LocationAvailability::TYPE_OPERATION_TIME]);
    }

    public function location($locationId)
    {
        return $this->andWhere(['location_availability.locationId' => $locationId]);
    }

    public function day($day)
    {
        return $this->andWhere(['location_availability.day' => $day]);
    }

    public function type($type)
    {
        return $this->andWhere(['location_availability.type' => $type]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['location_availability.isDeleted' => false]);
    }
}
