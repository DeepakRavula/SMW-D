<?php

use yii\db\Migration;

/**
 * Class m180720_112311_add_isDeletd_field
 */
class m180720_112311_add_isDeletd_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_invoice', 'isDeleted', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180720_112311_add_isDeletd_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180720_112311_add_isDeletd_field cannot be reverted.\n";

        return false;
    }
    */
}
