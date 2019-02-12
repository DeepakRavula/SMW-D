<?php

use yii\db\Migration;
use common\models\ItemType;
use common\models\ItemCategory;

/**
 * Class m190212_075212_adding_payment_credit_item_type
 */
class m190212_075212_adding_payment_credit_item_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $itemType = new ItemType();
        $itemType->name = 'Payment Credit';
        $itemType->save();
        $itemCategory = new ItemCategory();
        $itemCategory->name = 'Payment Credit';
        $itemCategory->isDeleted = false;
        $itemCategory->save();

        $item = new Item();
        $item->itemCategoryId = $itemCategory->id;
        $item->locationId = 0;
        $item->code = 'PAYMENT CREDIT';
        $item->description = 'Payment Credit';
        $item->price = 0.00;
        $item->royaltyFree = false;
        $item->taxStatusId = 2;
        $item->status = 1 ;
        $item->isDeleted = false;
        $item-save();
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190212_075212_adding_payment_credit_item_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190212_075212_adding_payment_credit_item_type cannot be reverted.\n";

        return false;
    }
    */
}
