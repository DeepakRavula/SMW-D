<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use yii\helpers\Console;
use Carbon\Carbon;
use common\models\LessonOwing;
/**
 * Class m190214_093253_adding_duedate_for_lessons
 */
class m190214_093253_adding_duedate_for_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('lesson', 'dueDate', $this->date()->notNull());
      }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190214_093253_adding_duedate_for_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190214_093253_adding_duedate_for_lessons cannot be reverted.\n";

        return false;
    }
    */
}
