<?php

use yii\db\Migration;

class m170809_100218_vacation_add_isDeleted extends Migration
{
    public function up()
    {
        $this->addColumn(
            'vacation',
            'isDeleted',
            $this->boolean()->after('isConfirmed')
        );
    }

    public function down()
    {
        echo "m170809_100218_vacation_add_isDeleted cannot be reverted.\n";

        return false;
    }
}
