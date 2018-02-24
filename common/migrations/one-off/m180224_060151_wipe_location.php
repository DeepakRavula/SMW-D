<?php

use yii\db\Migration;

/**
 * Class m180224_060151_wipe_location
 */
class m180224_060151_wipe_location extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180224_060151_wipe_location cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180224_060151_wipe_location cannot be reverted.\n";

        return false;
    }
    */
}
