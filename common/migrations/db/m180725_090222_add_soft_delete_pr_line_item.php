<?php

use yii\db\Migration;

/**
 * Class m180725_090222_add_soft_delete_pr_line_item
 */
class m180725_090222_add_soft_delete_pr_line_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_line_item', 'isDeleted', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180725_090222_add_soft_delete_pr_line_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180725_090222_add_soft_delete_pr_line_item cannot be reverted.\n";

        return false;
    }
    */
}
