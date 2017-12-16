<?php

use yii\db\Migration;

class m171216_073013_log_link extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log_link');
        if($tableSchema === null) {
            $this->createTable('log_link', [
                'id' => $this->primaryKey()
            ]);
            $this->addColumn('log_link', 'logId', $this->integer()->notNull()->after('id'));
            $this->addColumn('log_link', 'index', $this->string()->notNull()->after('logId'));
            $this->addColumn('log_link', 'baseUrl', $this->text()->after('index'));
             $this->addColumn('log_link', 'path', $this->text()->after('baseUrl'));
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
