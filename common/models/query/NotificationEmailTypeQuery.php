<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Holiday]].
 *
 * @see \common\models\Holiday
 */
class NotificationEmailTypeQuery extends ActiveQuery
{
    /*public function active()
    {
    return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Holiday[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Holiday|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['notification_email_type.isDeleted' => false]);
    }
}