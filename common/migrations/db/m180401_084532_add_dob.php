<?php

use yii\db\Migration;

/**
 * Class m180401_084532_add_dob
 */
class m180401_084532_add_dob extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('user_profile', 'birthDate', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180401_084532_add_dob cannot be reverted.\n";

        return false;
    }
}
