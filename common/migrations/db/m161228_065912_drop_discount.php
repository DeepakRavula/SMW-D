<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `discount`.
 */
class m161228_065912_drop_discount extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('discount', [
            'id' => $this->primaryKey(),
        ]);
    }
}
