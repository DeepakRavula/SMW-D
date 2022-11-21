<?php

use yii\db\Migration;

/**
 * Class m180609_055058_new_pfi_schema_changes_
 */
class m180609_055058_new_pfi_schema_changes_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    // public function safeUp()
    // {

    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function safeDown()
    // {
    //     echo "m180609_055058_new_pfi_schema_changes_ cannot be reverted.\n";

    //     return false;
    // }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        
        $tableSchema = Yii::$app->db->schema->getTableSchema('proforma_invoice');
        if ($tableSchema) {
            $this->dropTable('proforma_invoice');
        }
        if ($tableSchema == null) {
            $this->createTable('proforma_invoice', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'locationId' => $this->integer()->notNull(),
                'date' => $this->date(),
                'proforma_invoice_number' => $this->integer()->notNull(),
                'notes' => $this->text()
            ]);
        }
        $Pfi_Line_Item = Yii::$app->db->schema->getTableSchema('proforma_line_item');
        if ($Pfi_Line_Item) {
            $this->dropTable('proforma_line_item');
        }
        if ($Pfi_Line_Item == null) {
            $this->createTable('proforma_line_item', [
                'id' => $this->primaryKey(),
                'proformaInvoiceId' => $this->integer()->notNull(),
            ]);
        }
        $proforma_item_lesson = Yii::$app->db->schema->getTableSchema('proforma_item_lesson');
        if ($proforma_item_lesson) {
            $this->dropTable('proforma_item_lesson');
        }
        if ($proforma_item_lesson == null) {
            $this->createTable('proforma_item_lesson', [
                'id' => $this->primaryKey(),
                'proformaLineItemId' => $this->integer()->notNull(),
                'lessonId' => $this->integer()->notNull(),
            ]);
        }
        $proforma_item_invoice = Yii::$app->db->schema->getTableSchema('proforma_item_invoice');
        if ($proforma_item_invoice) {
            $this->dropTable('proforma_item_invoice');
        }
        if ($proforma_item_lesson == null) {
            $this->createTable('proforma_item_invoice', [
                'id' => $this->primaryKey(),
                'proformaLineItemId' => $this->integer()->notNull(),
                'invoiceId' => $this->integer()->notNull(),
            ]);
        }
 

    }

    public function down()
    {
        echo "m180609_055058_new_pfi_schema_changes_ cannot be reverted.\n";

        return false;
    }

}
