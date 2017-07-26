<?php

use yii\db\Migration;
use common\models\Item;

class m170726_112453_set_lesson_item_royalty_free extends Migration
{
    public function up()
    {
        $lessonItem = Item::findOne(['code' => Item::LESSON_ITEM]);
        $lessonCreditItem = Item::findOne(['code' => Item::LESSON_CREDIT]);
        $openingBalanceItem = Item::findOne(['code' => Item::OPENING_BALANCE_ITEM]);
        $lessonItem->royaltyFree = false;
        $lessonItem->save();
        $lessonCreditItem->royaltyFree = false;
        $lessonCreditItem->save();
        $openingBalanceItem->royaltyFree = false;
        $openingBalanceItem->save();
        $lineItems = InvoiceLineItem::find()
            ->where(['item_id' => $lessonItem->id])
            ->all();
        foreach ($lineItems as $lineItem) {
            $lineItem->royaltyFree = false;
            $lineItem->save();
        }
    }

    public function down()
    {
        echo "m170726_112453_set_lesson_item_royalty_free cannot be reverted.\n";

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
