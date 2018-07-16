<?php

use yii\db\Migration;

/**
 * Class m180710_064251_add_tax_field
 */
class m180710_064251_add_tax_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('lesson', 'tax', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180710_064251_add_tax_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180710_064251_add_tax_field cannot be reverted.\n";

        return false;
    }
    */
}
