<?php

use yii\db\Migration;
use common\models\CalendarEventColor;

/**
 * Class m190206_063439_add_absent_lesson_color
 */
class m190206_063439_add_absent_lesson_color extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $calendarEventColor = new CalendarEventColor();
        $calendarEventColor->name = 'Absent Lesson';
        $calendarEventColor->code = '#ff0000';
        $calendarEventColor->cssClass = 'absent-lesson';
        $calendarEventColor->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190206_063439_add_absent_lesson_color cannot be reverted.\n";

        return false;
    }
}
