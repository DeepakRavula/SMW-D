<?php

use yii\db\Migration;

class m171216_070906_log extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log');
        if ($tableSchema === null) {
            $this->createTable(
                'log',
                [
                'id' => $this->primaryKey(),
                'logObjectId' => $this->integer()->notNull(),
                'logActivityId' => $this->integer()->notNull(),
                'message' => $this->text(),
                'data' => $this->text(),
                'locationId' => $this->integer()->notNull(),
                'createdOn' => $this->timestamp(),
                'createdUserId' => $this->integer()
            ]
            );
        }
    }

    public function down()
    {
        echo "m171216_070906_log cannot be reverted.\n";

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
