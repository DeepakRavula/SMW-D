<?php

use yii\db\Migration;

class m170714_072516_add_isDeleted_column extends Migration
{
    public function up()
    {
        $this->addColumn(
            'teacher_unavailability',
            'isDeleted',
            $this->integer()->notNull()->after('toTime')
        );
    }

    public function down()
    {
        echo "m170714_072516_add_isDeleted_column cannot be reverted.\n";

        return false;
    }
}
