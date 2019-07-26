<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m190726_060034_fix_explode_lesson_with_program_rate
 */
class m190726_060034_fix_explode_lesson_with_program_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locationIds = [1,4,5,6,9,13,14,15,16,17,18,19,20,21];
        $explodedLessons = Lesson::find()
        ->exploded()
        ->location($locationIds)
        ->andWhere(['NOT', ['lesson.p' => Yii::$app->user->id]]);
        ->all();
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190726_060034_fix_explode_lesson_with_program_rate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190726_060034_fix_explode_lesson_with_program_rate cannot be reverted.\n";

        return false;
    }
    */
}
