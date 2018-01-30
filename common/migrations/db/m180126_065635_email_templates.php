<?php

use yii\db\Migration;

class m180126_065635_email_templates extends Migration
{
    public function up()
    {
        $this->createTable('email_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
        $this->insert('email_type', [
            'name' => 'Course',
        ]);
        $this->insert('email_type', [
            'name' => 'Lesson',
        ]);
        $this->insert('email_type', [
            'name' => 'ProformaInvoice',
        ]);
        $this->insert('email_type', [
            'name' => 'Invoice',
        ]);
        $this->createTable('email_template', [
            'id' => $this->primaryKey(),
            'emailTypeId' => $this->integer()->notNull(),
            'template' => $this->text()->notNull(),
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
