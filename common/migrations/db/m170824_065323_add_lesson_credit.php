<?php

use yii\db\Migration;

class m170824_065323_add_lesson_credit extends Migration
{
    public function up()
    {
		$tableSchema = Yii::$app->db->schema->getTableSchema('lesson_credit');
		if($tableSchema == null) {	
			$this->createTable('lesson_credit', [
				'id' => $this->primaryKey(),
				'lessonId' => $this->integer()->notNull(),
				'paymentId' => $this->integer()->notNull(),
			]);
		}
    }

    public function down()
    {
        echo "m170824_065323_add_lesson_credit cannot be reverted.\n";

        return false;
    }
}
