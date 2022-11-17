<?php

use yii\db\Migration;
use common\models\ProformaInvoice;

/**
 * Class m180721_073959_add_date_adjusted_pr
 */
class m180721_073959_add_date_adjusted_pr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_invoice', 'isDueDateAdjusted', $this->boolean()->notNull());
        $prs = ProformaInvoice::find()->all();
        foreach ($prs as $pr) {
            $pr->updateAttributes(['isDueDateAdjusted' => false]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180721_073959_add_date_adjusted_pr cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180721_073959_add_date_adjusted_pr cannot be reverted.\n";

        return false;
    }
    */
}
