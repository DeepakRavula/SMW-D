<?php

namespace common\models\query;

use common\models\Program;
use common\models\Course;

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
    
    public function needToRenewal($priorDate)
    {
        return $this->andWhere(['DATE(course.endDate)' => $priorDate->format('Y-m-d')]);
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

    public function groupProgram()
    {
        return $this->joinWith(['program' => function ($query) {
            $query->andWhere(['program.type' => Program::TYPE_GROUP_PROGRAM]);
        }]);
    }

    public function program($programId)
    {
        return $this->andWhere(['course.programId' => $programId]);
    }
    
    public function student($studentId)
    {
        return $this->joinWith(['enrolment' => function ($query) use ($studentId) {
            $query->andWhere(['enrolment.studentId' => $studentId]);
        }]);
    }

    public function privateProgram()
    {
        return $this->joinWith(['program' => function ($query) {
            $query->andWhere(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
        }]);
    }

    public function isConfirmed()
    {
        return $this->andWhere(['course.isConfirmed' => true]);
    }
    
    public function location($locationId)
    {
        return $this->andWhere(['locationId' => $locationId]);
    }

    public function confirmed()
    {
        return $this->andWhere(['course.isConfirmed' => true]);
    }
    
    public function between($fromDate, $toDate)
    {
        $this->andWhere(['between', 'DATE(startDate)', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            
        return $this;
    }
    
    public function betweenEndDate($fromDate, $toDate)
    {
        $this->andWhere(['between', 'DATE(endDate)', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            
        return $this;
    }
    
    public function regular()
    {
        return $this->andWhere(['course.type' => Course::TYPE_REGULAR]);
    }
    
    public function extra()
    {
        return $this->andWhere(['course.type' => Course::TYPE_EXTRA]);
    }

    public function overlap($from, $to) 
    {
        return $this->andWhere(['OR',
                    [
                            'between', 'DATE(course.startDate)', $from, $to
                    ],
                    [
                            'between','DATE(course.endDate)' , $from, $to
                    ],
                    [
                            'AND',
                            [
                                    '<', 'DATE(course.startDate)', $from
                            ],
                            [
                                    '>', 'DATE(course.endDate)', $to
                            ]

                    ]
            ]);
    }
    
    public function notDeleted()  
    {
        return $this->andwhere(['course.isDeleted' => false]);
    }
}
