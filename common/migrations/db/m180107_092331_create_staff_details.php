<?php

use yii\db\Migration;

class m180107_092331_create_staff_details extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('user_pin');
        if ($tableSchema == null) {
            $this->createTable('user_pin', [
                        'id' => $this->primaryKey(),
                        'userId' => $this->integer()->notNull(),
                        'pin' => $this->integer()->notNull()
                ]);
        }
    }

    public function down()
    {
        echo "m180107_092331_create_staff_details cannot be reverted.\n";

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
