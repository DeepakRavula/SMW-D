<?php

namespace common\models\query;

use common\models\Lesson;

/**
 * This is the ActiveQuery class for [[Lesson]].
 *
 * @see Lesson
 */
class LessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Lesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Lesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return $this
     */
    public function unInvoiced()
    {
		$this->joinWith(['invoiceLineItem' => function($query) {
			$query->joinWith('invoice');
			$query->where(['invoice.id' => null]);
		}]);
		
        return $this;
    }

	public function completed() {
		$this->andFilterWhere(['<=', 'l.date', (new \DateTime())->format('Y-m-d')]);
		
		return $this;
	}

	public function scheduled() {
		$this->andFilterWhere(['>', 'l.date', (new \DateTime())->format('Y-m-d')]);
		
		return $this;
	}

	public function location($locationId) {
		$this->joinWith(['enrolmentScheduleDay' => function($query) use($locationId) {
			$query->joinWith('enrolment');
			$query->andFilterWhere(['enrolment.location_id' => $locationId]);
		}]);
		
		return $this;
	}
}
