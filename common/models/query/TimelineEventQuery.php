<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/5/14
 * Time: 10:46 AM.
 */

namespace common\models\query;

use yii\db\ActiveQuery;

class TimelineEventQuery extends ActiveQuery
{
	public function location($locationId)
    {
        $this->andWhere(['locationId' => $locationId]);

        return $this;
    }
	
    public function today()
    {
        $this->andWhere(['>=', 'created_at', strtotime('today midnight')]);

        return $this;
    }

	public function lesson()
	{
		$this->joinWith(['timelineEventLesson' => function($query){
			$query->innerJoinWith('lesson');
		}]);
		
		return $this;
	}

	public function enrolment()
	{
		$this->joinWith(['timelineEventEnrolment' => function($query){
			$query->innerJoinWith('enrolment');
		}]);
		
		return $this;
	}
	public function student()
	{
		$this->joinWith(['timelineEventStudent' => function($query){
			$query->innerJoinWith('student');
		}]);
		
		return $this;
	}
	public function invoice()
	{
		$this->joinWith(['timelineEventInvoice' => function($query){
			$query->innerJoinWith('invoice');
		}]);
		
		return $this;
	}
}
