<?php

use yii\db\Migration;
use common\models\ItemType;
use common\models\Invoice;

/**
 * Class m180515_102028_fix_lesson_invoice_date
 */
class m180515_102028_fix_lesson_invoice_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invoices = Invoice::find()
            ->notDeleted()
            ->invoice()
            ->location([14, 15])
            ->joinWith(['lineItem' => function ($query) {
                $query->andWhere(['invoice_line_item.item_type_id' => [ItemType::TYPE_EXTRA_LESSON,
                    ItemType::TYPE_GROUP_LESSON, ItemType::TYPE_PRIVATE_LESSON]
                ]);
            }])
            ->all();
        foreach ($invoices as $invoice) {
            $invoice->updateAttributes([
                'date' => (new \DateTime($invoice->lineItem->lesson->date))->format('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_102028_fix_lesson_invoice_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180515_102028_fix_lesson_invoice_date cannot be reverted.\n";

        return false;
    }
    */
}
