<?php

use yii\db\Migration;
use common\models\TeacherUnavailability;

/**
 * Class m190813_093436_add_fromdatetime_todatetime
 */
class m190813_093436_add_fromdatetime_todatetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('teacher_unavailability', 'fromDateTime', $this->timestamp()->after('reason'));
        $this->addColumn('teacher_unavailability', 'toDateTime', $this->timestamp()->after('reason'));
        $teacherUnavailabilities = TeacherUnavailability::find()
                ->all();
        foreach ($teacherUnavailabilities as $teacherUnavailability) {
            $fromDateTime = $teacherUnavailability->fromDate . ' ' . $teacherUnavailability->fromTime;
            $toDateTime = $teacherUnavailability->toDate . ' ' . $teacherUnavailability->toTime;
            $teacherUnavailability->updateAttributes(['fromDateTime' => $fromDateTime, 'toDateTime' => $toDateTime]);
        }

        $this->dropColumn('teacher_unavailability', 'fromDate');
        $this->dropColumn('teacher_unavailability', 'toDate');
        $this->dropColumn('teacher_unavailability', 'fromTime');
        $this->dropColumn('teacher_unavailability', 'toTime');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190813_093436_add_fromdatetime_todatetime cannot be reverted.\n";

        return false;
    }
}
