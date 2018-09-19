<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;

/**
 * Class m180919_091124_add_old_teacher_rate
 */
class m180919_091124_add_old_teacher_rate extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('lesson', 'teacherRateOld', $this->decimal(10, 4)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180919_091124_add_old_teacher_rate cannot be reverted.\n";

        return false;
    }
}
