<?php

use yii\db\Migration;

/**
 * Class m180316_100142_remove_reminder_notes
 */
class m180316_100142_remove_reminder_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropColumn('invoice', 'reminderNotes');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180316_100142_remove_reminder_notes cannot be reverted.\n";

        return false;
    }
}
