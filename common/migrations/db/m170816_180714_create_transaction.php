<?php

use yii\db\Migration;

class m170816_180714_create_transaction extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('transaction');
        if ($tableSchema == null) {
            $this->createTable('transaction', [
                'id' => $this->primaryKey()
            ]);
        }
        $this->addColumn('invoice', 'transactionId', $this->integer()->null()->after('id'));
        $this->addColumn('payment', 'transactionId', $this->integer()->null()->after('id'));
        $this->addColumn('payment', 'transactionDummy', $this->integer()->null()->after('isDeleted'));
    }

    public function down()
    {
        echo "m170816_180714_create_transaction cannot be reverted.\n";

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
