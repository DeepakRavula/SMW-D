<?php

use yii\db\Migration;

class m170810_053943_explode_lesson_refactor extends Migration
{
    public function up()
    {
        $this->renameColumn('lesson_split_usage', 'lessonSplitId', 'lessonId');
        $this->addColumn(
            'lesson',
            'isExploded',
            $this->integer()->notNull()->after('type')
        );
        $tableSchema = Yii::$app->db->schema->getTableSchema('lesson_hierarchy');
        if ($tableSchema == null) {
            $this->createTable('lesson_hierarchy', [
                    'id' => $this->primaryKey(),
                    'lessonId' => $this->integer()->notNull(),
                    'childLessonId' => $this->integer()->notNull(),
                    'depth' => $this->integer()->notNull(),
            ]);
        }
    }

    public function down()
    {
        echo "m170810_053943_explode_lesson_refactor cannot be reverted.\n";

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
