<?php

namespace common\models\query;

use common\models\Program;
use yii\db\ActiveQuery;

/**
 * Class UserQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class ProgramQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => Program::STATUS_ACTIVE]);

        return $this;
    }

    public function privateProgram()
    {
        $this->andWhere(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);

        return $this;
    }

    public function group()
    {
        $this->andWhere(['program.type' => Program::TYPE_GROUP_PROGRAM]);

        return $this;
    }
    
    public function enrollment($enrolmentIds)
    {
        return $this->joinWith(['course' => function ($query) use ($enrolmentIds) {
            $query->joinWith(['enrolments' => function ($query) use ($enrolmentIds) {
                $query->andWhere(['enrolment.id' => $enrolmentIds])
                        ->notDeleted()
                        ->isConfirmed();
            }])
                ->isConfirmed()
                ->notDeleted();
        }]);
    }

    public function studentEnrolled($studentId)
    {
        return $this->joinWith(['course' => function ($query) use ($studentId) {
            $query->joinWith(['enrolments' => function ($query) use ($studentId) {
                $query->andWhere(['enrolment.studentId' => $studentId])
                        ->notDeleted()
                        ->isConfirmed();
            }])
                ->isConfirmed()
                ->notDeleted();
        }]);
    }

    public function notDeleted() 
    {
        return $this->andWhere(['program.isDeleted' =>  false]);
    }
}
