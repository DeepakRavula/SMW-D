<?php

use yii\db\Migration;

/**
 * Class m180920_053925_add_qualification_audit_log
 */
class m180920_053925_add_qualification_audit_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('qualification', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('qualification', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('qualification', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('qualification', 'updatedByUserId', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180920_053925_add_qualification_audit_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180920_053925_add_qualification_audit_log cannot be reverted.\n";

        return false;
    }
    */
}
