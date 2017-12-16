<?php

use yii\db\Migration;

class m171216_070906_log extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('log');
        if($tableSchema === null) {
            $this->createTable('log', [
                'id' => $this->primaryKey()
            ]);
            $this->addColumn('log', 'logObjectId', $this->integer()->notNull()->after('id'));
            $this->addColumn('log', 'logActivityId', $this->integer()->notNull()->after('logObjectId'));
            $this->addColumn('log', 'message', $this->text()->after('logActivityId'));
            $this->addColumn('log', 'data', $this->text()->after('message'));
            $this->addColumn('log', 'locationId', $this->integer()->notNull()->after('data'));
            $this->addColumn('log', 'createdOn', $this->timestamp()->after('locationId'));
            $this->addColumn('log', 'createdUserId', $this->integer()->after('createdOn'));

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
