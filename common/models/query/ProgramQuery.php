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
}
