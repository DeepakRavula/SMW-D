<?php

use yii\db\Migration;
use common\models\Lesson;

class m171106_155138_add_ispresent extends Migration
{
    public function up()
    {
        $this->addColumn(
            'lesson',
            'isPresent',
            $this->boolean()->notNull()->after('status')
        );
        $lessons = Lesson::find()->all();
        foreach ($lessons as $lesson) {
            if ((int)$lesson->status === 6) {
                $lesson->updateAttributes([
                    'isPresent' => false
                ]);
            } else {
                $lesson->updateAttributes([
                    'isPresent' => true
                ]);
            }
        }
    }

    public function down()
    {
        echo "m171106_155138_add_ispresent cannot be reverted.\n";

        return false;
    }
}
