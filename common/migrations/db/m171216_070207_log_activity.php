<?php

use yii\db\Migration;

class m171216_070207_log_activity extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log_activity');
        if ($tableSchema === null) {
            $this->createTable(
                'log_activity',
                [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull()
            ]
            );
        }
    }

    public function down()
    {
        echo "m171216_070207_log_activity cannot be reverted.\n";

        return false;
    }
    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
