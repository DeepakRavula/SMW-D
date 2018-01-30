<?php

use yii\db\Migration;

class m171216_075734_log_history extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log_history');
        if ($tableSchema === null) {
            $this->createTable(
                'log_history',
                [
                'id' => $this->primaryKey(),
                'logId' => $this->integer(),
                'instanceId' => $this->integer(),
                'instanceType' => $this->string(),
            ]
            );
        }
    }

    public function down()
    {
        echo "m171216_075734_log_history cannot be reverted.\n";

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
