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
        $this->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM]);

        return $this;
    }

	public function group()
    {
        $this->andWhere(['type' => Program::TYPE_GROUP_PROGRAM]);

        return $this;
    }
}
