<?php

namespace common\models\query;

use common\models\Program;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Student]].
 *
 * @see \common\models\Student
 */
class StudentQuery extends ActiveQuery
{
    
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Student|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

	public function notDeleted() {
		$this->where(['student.isDeleted' => false]);
		
		return $this;
	}
	
	public function location($locationId) {
		$this->joinWith(['customer' => function($query) use($locationId){
				$query->joinWith('userLocation')
					->where(['user_location.location_id' => $locationId]);
			}]);
		
		return $this;
	}
	
	public function enrolled($currentDate) {
		$this->joinWith(['enrolment' => function($query) use($currentDate){
				$query->joinWith(['course' => function($query) use($currentDate){
                   $query->andWhere(['>=','course.endDate', $currentDate]);
				}])
            ->andWhere(['not', ['enrolment.studentId' => null]]);
			}]);
		
		return $this;
	}

	public function groupCourseEnrolled($courseId) {
		$this->joinWith(['enrolment' => function($query)  use($courseId){
				$query->andWhere(['courseId' => $courseId])
					->where(['not',['studentId' => null]]);
			}]);
		
		return $this;
	}

	public function unenrolled($courseId) {
		$this->joinWith(['enrolment' => function($query)  use($courseId){
				$query->where(['courseId' => $courseId])
					->where(['studentId' => null]);
			}]);
		
		return $this;
	}
	
	public function teacherStudents($locationId, $id) {
		$this->joinWith(['enrolment' => function($query) use($id, $locationId){
			$query->joinWith(['lessons' => function($query) use($id){
				$query->where(['lesson.teacherId' => $id])
					->groupBy('lesson.teacherId');	
			}])
			->joinWith(['course' => function($query) use($locationId){	
				$query->where(['locationId' => $locationId]);
			}]);
		}]);
		
		return $this;
	}
}
