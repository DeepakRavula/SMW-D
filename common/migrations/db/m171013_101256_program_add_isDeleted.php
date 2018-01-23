<?php

use yii\db\Migration;

class m171013_101256_program_add_isDeleted extends Migration
{
    public function up()
    {
        $this->addColumn('program', 'isDeleted', $this->boolean()->after('type'));
    }

    public function down()
    {
        echo "m171013_101256_program_add_isDeleted cannot be reverted.\n";

        return false;
    }
}
