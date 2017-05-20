<?php

namespace common\models\query;

use common\models\Program;

/**
 * This is the ActiveQuery class for [[\common\models\Course]].
 *
 * @see \common\models\Course
 */
class CourseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Course[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Course|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function groupProgram($locationId)
    {
        $this->joinWith(['program' => function ($query) use ($locationId) {
            $query->where(['type' => Program::TYPE_GROUP_PROGRAM]);
        }])
        ->where(['locationId' => $locationId]);

        return $this;
    }

	public function isConfirmed()
    {
        return $this->andWhere(['course.isConfirmed' => true]);
    }
	
	public function location($locationId) {
		return $this->andWhere(['locationId' => $locationId]);
	}

	public function confirmed() {
		return $this->andWhere(['course.isConfirmed' => true]);
	}
	public function between($fromDate, $toDate)
	{
		$this->andWhere(['OR', 
			[
				'between', 'DATE(startDate)', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')
			],
			[
				'between', 'DATE(endDate)', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')
			],
		]);
		return $this;
	}
}
