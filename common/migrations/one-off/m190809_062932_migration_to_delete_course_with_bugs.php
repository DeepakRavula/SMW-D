<?php

use yii\db\Migration;
use common\models\Enrolment;
use common\models\Course;
use Carbon\Carbon;

/**
 * Class m190809_062932_migration_to_delete_course_with_bugs
 */
class m190809_062932_migration_to_delete_course_with_bugs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $course = Course::findOne(['id' => 7834]);
        $course->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190809_062932_migration_to_delete_course_with_bugs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190809_062932_migration_to_delete_course_with_bugs cannot be reverted.\n";

        return false;
    }
    */
}
