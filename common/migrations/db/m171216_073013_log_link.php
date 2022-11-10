<?php

use yii\db\Migration;

class m171216_073013_log_link extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log_link');
        if ($tableSchema === null) {
            $this->createTable(
                'log_link',
                [
                'id' => $this->primaryKey(),
                'logId' => $this->integer()->notNull(),
                'index' => $this->string()->notNull(),
                'baseUrl' => $this->text(),
                'path' => $this->text(),
            ]
            );
        }
    }

    public function down()
    {
        echo "m171216_073013_log_link cannot be reverted.\n";

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
