<?php

use yii\db\Migration;

class m180126_065635_email_templates extends Migration
{
    public function up()
    {
        $this->createTable('email_object', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
        $this->insert('email_object', [
            'name' => 'Course',
        ]);
        $this->insert('email_object', [
            'name' => 'Lesson',
        ]);
        $this->insert('email_object', [
            'name' => 'ProformaInvoice',
        ]);
        $this->insert('email_object', [
            'name' => 'Invoice',
        ]);
        $this->createTable('email_template', [
            'id' => $this->primaryKey(),
            'emailTypeId' => $this->integer()->notNull(),
            'subject' => $this->text()->notNull(),
            'header' => $this->text()->notNull(),
            'footer' => $this->text()->notNull(),
            'updatedAt' => $this->timestamp()->defaultValue(null),
            'createdAt' => $this->timestamp()->defaultValue(null),
        ]);
    }

    public function down()
    {
        echo "m180126_065635_email_templates cannot be reverted.\n";

        return false;
    }
}
