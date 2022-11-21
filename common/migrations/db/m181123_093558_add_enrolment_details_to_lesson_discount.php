<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;

/**
 * Class m181123_093558_add_enrolment_details_to_lesson_discount
 */
class m181123_093558_add_enrolment_details_to_lesson_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('lesson_discount');
        if (!isset($table->columns['enrolmentId'])) {
            $this->addColumn('lesson_discount', 'enrolmentId', $this->integer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181123_093558_add_enrolment_details_to_lesson_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181123_093558_add_enrolment_details_to_lesson_discount cannot be reverted.\n";

        return false;
    }
    */
}
