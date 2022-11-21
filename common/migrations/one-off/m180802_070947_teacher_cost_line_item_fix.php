<?php

use yii\db\Migration;
use common\models\User;
use common\models\InvoiceLineItem;
use common\models\Lesson;
use common\models\Qualification;
use common\models\CourseProgramRate;

/**
 * Class m180802_070947_teacher_cost_line_item_fix
 */
class m180802_070947_teacher_cost_line_item_fix extends Migration
{
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $course = CourseProgramRate::findOne(1055);
        $course->updateAttributes([
            'programRate' => 57.50
        ]);

        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['OR', ['lesson.programRate' => 0.0000], ['<', 'lesson.programRate', 0]])
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->courseProgramRate) {
                $rate = $lesson->courseProgramRate->programRate;
            } else {
                $rate = $lesson->program->rate;
            }
            $lesson->updateAttributes([
                'programRate' => $rate
            ]);
        }

        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['OR', ['lesson.teacherRate' => 0.0000], ['<', 'lesson.teacherRate', 0]])
            ->all();
        foreach ($lessons as $lesson) {
            $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
                    'program_id' => $lesson->course->program->id]);
            $lesson->updateAttributes([
                'teacherRate' => !empty($qualification->rate) ? $qualification->rate : 0
            ]);
        }

        $locationId = [14, 15];
        $lessonLineItems = InvoiceLineItem::find()
            ->lessonItem()
            ->joinWith(['invoice' => function ($query) use ($locationId) {
                $query->location($locationId)
                    ->invoice()
                    ->notDeleted();
            }])
            ->notDeleted()
            ->all();
        foreach ($lessonLineItems as $lessonLineItem) {
            if ($lessonLineItem->lesson) {
                $lessonLineItem->updateAttributes([
                    'rate' => $lessonLineItem->lesson->teacherRate,
                    'cost' => $lessonLineItem->lesson->teacherRate * $lessonLineItem->unit
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180802_070947_teacher_cost_line_item_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180802_070947_teacher_cost_line_item_fix cannot be reverted.\n";

        return false;
    }
    */
}
