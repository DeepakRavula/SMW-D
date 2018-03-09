<?php

use yii\db\Migration;

/**
 * Class m180228_192405_add_balance_student_csv
 */
class m180228_192405_add_balance_student_csv extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('student_csv', 'openingBalance');
        $this->addColumn('student_csv', 'openingBalance', $this->decimal(10,4)->notNull()->after('notes'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180228_192405_add_balance_student_csv cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180228_192405_add_balance_student_csv cannot be reverted.\n";

        return false;
    }
    */
}
