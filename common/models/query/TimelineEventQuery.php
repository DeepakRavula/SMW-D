<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/5/14
 * Time: 10:46 AM.
 */

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\TimelineEvent;

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
	
	public function invoice()
	{
		$this->joinWith(['timelineEventInvoice' => function($query){
			$query->innerJoinWith('invoice');
		}]);
		
		return $this;
	}
	public function payment()
	{
		$this->joinWith(['timelineEventPayment' => function($query){
			$query->innerJoinWith('payment');
		}]);
		
		return $this;
	}
	public function studentEnrolment($studentId)
	{
		$this->joinWith(['timelineEventEnrolment' => function($query) use($studentId){
			$query->innerJoinWith(['enrolment' => function($query) use($studentId){
				$query->joinWith(['student' => function($query) use($studentId) {
					$query->andWhere(['student.id' => $studentId]);
				}]);
			}]);
		}]);
		
		return $this;
	}
	public function studentLesson($studentId)
	{
		$this->joinWith(['timelineEventLesson' => function($query) use($studentId){
			$query->innerJoinWith(['lesson' => function($query) use($studentId){
				$query->joinWith(['enrolment' => function($query) use($studentId) {
					$query->joinWith(['student' => function($query) use($studentId) {
						$query->andWhere(['student.id' => $studentId]);
					}]);
				}]);
			}]);
		}]);
		
		return $this;
	}
	public function studentInvoice($studentId)
	{
		$this->joinWith(['timelineEventInvoice' => function($query) use($studentId){
			$query->innerJoinWith(['invoice' => function($query) use($studentId){
				$query->joinWith(['user' => function($query) use($studentId) {
					$query->joinWith(['student' => function($query) use($studentId) {
						$query->andWhere(['student.id' => $studentId]);
					}]);
				}]);
			}]);
		}]);
		
		return $this;
	}
	public function studentPayment($studentId)
	{
		$this->joinWith(['timelineEventPayment' => function($query) use($studentId){
			$query->innerJoinWith(['payment' => function($query) use($studentId){
				$query->innerJoinWith(['invoice' => function($query) use($studentId){
					$query->joinWith(['user' => function($query) use($studentId) {
						$query->joinWith(['student' => function($query) use($studentId) {
							$query->andWhere(['student.id' => $studentId]);
						}]);
					}]);
				}]);
			}]);
		}]);
		
		return $this;
	}
	public function student($studentId)
	{
		$this->joinWith(['timelineEventEnrolment' => function($query) use($studentId){
			$query->innerJoinWith(['enrolment' => function($query) use($studentId){
				$query->joinWith(['student' => function($query) use($studentId) {
					$query->andWhere(['student.id' => $studentId]);
				}]);
			}]);
		}]);

		$studentLog = TimelineEvent::find()
			->joinWith(['timelineEventStudent' => function($query) use($studentId){
				$query->andWhere(['studentId' => $studentId]);
			}]);
		$lessonLog = TimelineEvent::find()
			->joinWith(['timelineEventLesson' => function($query) use($studentId) {
				$query->joinWith(['lesson' => function($query) use($studentId) {
					$query->joinWith(['enrolment' => function($query) use($studentId) {
						$query->andWhere(['studentId' => $studentId]);
					}]);
				}]);
			}]);	
		$paymentLog = TimelineEvent::find()
			->joinWith(['timelineEventPayment' => function($query) use($studentId){
			$query->innerJoinWith(['payment' => function($query) use($studentId){
				$query->innerJoinWith(['invoice' => function($query) use($studentId){
					$query->joinWith(['user' => function($query) use($studentId) {
						$query->joinWith(['student' => function($query) use($studentId) {
							$query->andWhere(['student.id' => $studentId]);
						}]);
					}]);
				}]);
			}]);
		}]);
		$invoiceLog = TimelineEvent::find()
			->joinWith(['timelineEventInvoice' => function($query) use($studentId){
			$query->innerJoinWith(['invoice' => function($query) use($studentId){
				$query->joinWith(['user' => function($query) use($studentId) {
					$query->joinWith(['student' => function($query) use($studentId) {
						$query->andWhere(['student.id' => $studentId]);
					}]);
				}]);
			}]);
		}]);
		$this->union($studentLog);
		$this->union($lessonLog);
		$this->union($paymentLog);
		$this->union($invoiceLog);
		return $this;
	}
}
