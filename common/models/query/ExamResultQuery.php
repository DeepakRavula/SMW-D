<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Holiday]].
 *
 * @see \common\models\Holiday
 */
class ExamResultQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\ExamResult[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\City|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted() 
    {
        return $this->andWhere(['exam_result.isDeleted' => false]);
    }
}
