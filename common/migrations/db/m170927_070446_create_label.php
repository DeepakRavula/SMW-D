<?php

use yii\db\Migration;
use common\models\PhoneLabel;

class m170927_070446_create_label extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('label');
        if ($tableSchema == null) {
            $this->createTable('label', [
                'id' => $this->primaryKey()
            ]);
            $this->addColumn('label', 'name', $this->string()->notNull()->after('id'));
            $this->addColumn('label', 'userAdded', $this->integer()->notNull()->after('name'));
            $labels = PhoneLabel::find()->all();
            foreach ($labels as $label) {
                $this->insert('label', [
                    'name' => $label->name,
                    'userAdded' => 0,
                ]);
            }
        }
    }

    public function down()
    {
        echo "m170927_070446_create_label cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
