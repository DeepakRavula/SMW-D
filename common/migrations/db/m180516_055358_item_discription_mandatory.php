<?php

use yii\db\Migration;

/**
 * Class m180516_055358_item_discription_mandatory
 */
class m180516_055358_item_discription_mandatory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('item', 'description', 'TEXT NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180516_055358_item_discription_mandatory cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180516_055358_item_discription_mandatory cannot be reverted.\n";

        return false;
    }
    */
}
