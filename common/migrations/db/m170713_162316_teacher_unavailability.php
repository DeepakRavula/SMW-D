<?php

use yii\db\Migration;

class m170713_162316_teacher_unavailability extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('teacher_unavailability');
        if ($tableSchema == null) {
            $this->createTable('teacher_unavailability', [
                'id' => $this->primaryKey(),
                'teacherId' => $this->integer()->notNull(),
                'fromDate' => $this->date()->notNull(),
                'toDate' => $this->date()->notNull(),
                'fromTime' => $this->time(),
                'toTime' => $this->time(),
            ]);
        }
    }

    public function down()
    {
        echo "m170713_162316_teacher_unavailability cannot be reverted.\n";

        return false;
    }
}
