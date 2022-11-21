<?php

use yii\db\Migration;

/**
 * Class m180510_111943_double_teacher_cost
 */
class m180510_111943_double_teacher_cost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invoiceLineItems = InvoiceLineItem::find()
            ->notDeleted()
            ->andWhere(['item_type_Id'=> 6])
            ->joinWith(['invoice' => function ($query) {
                $query->notDeleted()
                    ->andWhere(['NOT', ['invoice.id' => null]]);
            }])
            ->all();
        foreach($invoiceLineItems as $invoiceLineItem)
        {
            $invoiceLineItem->cost=$invoiceLineItem->cost * 2.00;
            $invoiceLineItem->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180510_111943_double_teacher_cost cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_111943_double_teacher_cost cannot be reverted.\n";

        return false;
    }
    */
}
