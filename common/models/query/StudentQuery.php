<?php

namespace common\models\query;

use common\models\Student;
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
     * {@inheritdoc}
     *
     * @return \common\models\Student|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        $this->andWhere(['student.status' => Student::STATUS_ACTIVE]);

        return $this;
    }

    public function notDeleted()
    {
        return $this->andWhere(['student.isDeleted' => false]);
    }

    public function location($locationId)
    {
        $this->joinWith(['customer' => function ($query) use ($locationId) {
            $query->joinWith('userLocation')
               ->where(['user_location.location_id' => $locationId]);
        }]);

        return $this;
    }

    public function enrolled($currentDate)
    {
        $this->joinWith(['enrolment' => function ($query) use ($currentDate) {
            $query->joinWith(['course' => function ($query) use ($currentDate) {
                $query->andWhere(['>=', 'course.endDate', $currentDate]);
            }])
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['not', ['enrolment.studentId' => null]]);
        }]);

        return $this;
    }

    public function groupCourseEnrolled($courseId)
    {
        $this->joinWith(['enrolment' => function ($query) use ($courseId) {
            $query->joinWith(['course' => function ($query) use ($courseId) {
                $query->where(['course.id' => $courseId]);
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
                    ->where(['user_location.location_id' => $locationId]);
            }]);

        $enrolledStudents = Student::find()
            ->notDeleted()
            ->select(['student.id', 'student.first_name', 'student.last_name'])
            ->joinWith(['enrolment' => function ($query) use ($courseId) {
                $query->joinWith(['course' => function ($query) use ($courseId) {
                    $query->where(['course.id' => $courseId]);
                }]);
            }]);

        $query = Student::find()
            ->notDeleted()
            ->select(['loc_student.id', 'loc_student.first_name', 'loc_student.last_name'])->from(['loc_student' => $studentLocation])
            ->leftJoin(['enrolled_student' => $enrolledStudents], 'loc_student.id = enrolled_student.id')
            ->where(['enrolled_student.id' => null]);

        return $query;
    }

    public function teacherStudents($locationId, $id)
    {
        $this->joinWith(['enrolment' => function ($query) use ($id, $locationId) {
            $query->joinWith(['course' => function ($query) use ($locationId, $id) {
                $query->andWhere(['locationId' => $locationId, 'teacherId' => $id]);
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
