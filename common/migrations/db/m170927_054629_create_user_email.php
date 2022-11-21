<?php

use yii\db\Migration;

class m170927_054629_create_user_email extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('user_email');
        if ($tableSchema == null) {
            $this->createTable('user_email', [
                'id' => $this->primaryKey()
            ]);
            $this->addColumn('user_email', 'userId', $this->integer()->notNull()->after('id'));
            $this->addColumn('user_email', 'email', $this->string()->notNull()->after('userId'));
            $this->addColumn('user_email', 'labelId', $this->integer()->notNull()->after('email'));
            $this->addColumn('user_email', 'isPrimary', $this->integer()->notNull()->after('labelId'));
        }
    }

    public function down()
    {
        echo "m170927_054629_create_user_email cannot be reverted.\n";

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
