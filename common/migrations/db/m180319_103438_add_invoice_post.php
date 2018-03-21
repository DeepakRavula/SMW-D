<?php

use yii\db\Migration;

/**
 * Class m180319_103438_add_invoice_post
 */
class m180319_103438_add_invoice_post extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('invoice', 'isPosted', $this->integer()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180319_103438_add_invoice_post cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180319_103438_add_invoice_post cannot be reverted.\n";

        return false;
    }
    */
}
