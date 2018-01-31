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
            'createdAt' => $this->timestamp()->notNull(),
            'updatedAt' => $this->timestamp()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m180126_065635_email_templates cannot be reverted.\n";

        return false;
    }
}
