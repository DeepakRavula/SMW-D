<?php

namespace common\models\query;

use common\models\Student;
use yii\db\ActiveQuery;
use common\models\Course;

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
     * {@inheritdoc}
     *
     * @return \common\models\Student|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['student.isDeleted' => false]);
    }

    public function draft()
    {
        return $this->andWhere(['student.status' => Student::STATUS_DRAFT]);
    }

    public function location($locationId)
    {
        return $this->joinWith(['customerLocation' => function ($query) use ($locationId) {
            $query->andWhere(['user_location.location_id' => $locationId]);
        }]);
    }

    public function statusActive()
    {
        return $this->andWhere(['student.status' => Student::STATUS_ACTIVE]);
    }

    public function active($fromDate = null, $toDate = null)
    {
        $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
        if (!$fromDate && !$toDate) {
            $fromDate = $currentDate;
            $toDate = $currentDate;
        }
        return $this->joinWith(['enrolments' => function ($query) use ($fromDate, $toDate) {
            $query->joinWith(['course' => function ($query) use ($fromDate, $toDate) {
                $query->joinWith(['lessons' => function ($query) {
                    $query->andWhere(['NOT', ['lesson.id' => null]]);
                }])
                    ->overlap($fromDate, $toDate)
		            ->regular()
                    ->confirmed()
                    ->notDeleted();
            }])
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['not', ['enrolment.studentId' => null]]);
        }]);
    }

    public function groupCourseEnrolled($courseId)
    {
        $this->joinWith(['enrolments' => function ($query) use ($courseId) {
            $query->joinWith(['course' => function ($query) use ($courseId) {
                $query->andWhere(['course.id' => $courseId])
                        ->confirmed()
                        ->notDeleted();
            }])
            ->andWhere(['NOT', ['studentId' => null]])
            ->isConfirmed();
        }]);

        return $this;
    }

    public function unenrolled($courseId, $locationId)
    {
        $studentLocation = Student::find()
            ->notDeleted()
            ->select(['student.id', 'student.first_name', 'student.last_name'])
            ->innerjoinWith(['customer' => function ($query) use ($locationId) {
                $query->innerjoinWith('userLocation')
                    ->andWhere(['user_location.location_id' => $locationId]);
            }]);

        $enrolledStudents = Student::find()
            ->notDeleted()
            ->select(['student.id', 'student.first_name', 'student.last_name'])
            ->joinWith(['enrolments' => function ($query) use ($courseId) {
                $query->joinWith(['course' => function ($query) use ($courseId) {
                    $query->andWhere(['course.id' => $courseId])
                        ->notDeleted();
                }]);
            }]);

        $query = Student::find()
            ->notDeleted()
            ->select(['loc_student.id', 'loc_student.first_name', 'loc_student.last_name'])
            ->from(['loc_student' => $studentLocation])
            ->leftJoin(['enrolled_student' => $enrolledStudents], 'loc_student.id = enrolled_student.id')
            ->andWhere(['enrolled_student.id' => null]);

        return $query;
    }

    public function teacherStudents($locationId, $id)
    {
        $this->joinWith(['enrolments' => function ($query) use ($id, $locationId) {
            $query->joinWith(['course' => function ($query) use ($locationId, $id) {
                $query->andWhere(['locationId' => $locationId, 'course.teacherId' => $id]);
            }])
            ->isConfirmed();
        }]);

        return $this;
    }

    public function customer($customerId)
    {
        return $this->andWhere(['student.customer_id' => $customerId]);
    }
}
