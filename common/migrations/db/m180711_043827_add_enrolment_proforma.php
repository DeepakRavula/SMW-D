<?php

use yii\db\Migration;
use common\models\ProformaItemLesson;

/**
 * Class m180711_043827_add_enrolment_proforma
 */
class m180711_043827_add_enrolment_proforma extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_item_lesson', 'enrolmentId', $this->integer()->notNull());
        $proformaItemLessons = ProformaItemLesson::find()->all();
        foreach ($proformaItemLessons as $proformaItemLesson) {
            $proformaItemLesson->updateAttributes(['enrolmentId' => $proformaItemLesson->lesson->enrolment->id]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180711_043827_add_enrolment_proforma cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180711_043827_add_enrolment_proforma cannot be reverted.\n";

        return false;
    }
    */
}
