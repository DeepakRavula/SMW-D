<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Holiday]].
 *
 * @see \common\models\Holiday
 */
class BlogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Blog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Blog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted() 
    {
        return $this->andWhere(['blog.isDeleted' => false]);
    }
}
