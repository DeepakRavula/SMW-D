<?php

use yii\db\Migration;

class m171216_064646_log_object extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log_object');
        if ($tableSchema === null) {
            $this->createTable(
                'log_object',
                [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull()
            ]
            );
        }
    }

    public function down()
    {
        echo "m171216_064646_log_object cannot be reverted.\n";

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
