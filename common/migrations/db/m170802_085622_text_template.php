<?php

use yii\db\Migration;

class m170802_085622_text_template extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('text_template');
        if ($tableSchema == null) {
            $this->createTable('text_template', [
                'id' => $this->primaryKey(),
                'message' => $this->text(),
                'type' => $this->integer()->notNull(),
            ]);
        }
    }

    public function down()
    {
        echo "m170802_085622_text_template cannot be reverted.\n";

        return false;
    }
}
