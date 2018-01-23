<?php

use yii\db\Migration;

class m170809_065434_lesson_add_isconfirmed extends Migration
{
    public function up()
    {
        $this->addColumn(
            'lesson',
            'isConfirmed',
            $this->boolean()->after('isDeleted')
        );
    }

    public function down()
    {
        echo "m170809_065434_lesson_add_isconfirmed cannot be reverted.\n";

        return false;
    }
}
