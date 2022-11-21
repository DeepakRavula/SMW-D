<?php

use yii\db\Migration;

class m171025_171741_remove_program_isdeleted_column extends Migration
{
    public function up()
    {
        $this->dropColumn('program', 'isDeleted');
    }

    public function down()
    {
        echo "m171025_171741_remove_program_isdeleted_column cannot be reverted.\n";

        return false;
    }
}
