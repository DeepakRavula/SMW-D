<?php

use yii\db\Migration;
use common\models\Enrolment;
use Carbon\Carbon;
/**
 * Class m190603_105113_add_isDeleted_in_group_lesson
 */
class m190603_105113_add_isDeleted_in_group_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $this->addColumn(
            'group_lesson',
            'isDeleted',
            $this->boolean()->notNull()->after('paidStatus')
        );

        $this->addColumn(
            'enrolment',
            'endDate',
            $this->timestamp()->notNull()->after('isDeleted')
        );

        $enrolments = Enrolment::find()->all();
        foreach ($enrolments as $enrolment) {
            if ($enrolment->course) {
            $enrolment->updateAttributes(['endDate' => Carbon::parse($enrolment->course->endDate)->format('Y-m-d')]);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190603_105113_add_isDeleted_in_group_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190603_105113_add_isDeleted_in_group_lesson cannot be reverted.\n";

        return false;
    }
    */
}
