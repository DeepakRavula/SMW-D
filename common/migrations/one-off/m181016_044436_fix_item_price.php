<?php

use yii\db\Migration;
use common\models\Item;

/**
 * Class m181016_044436_fix_item_price
 */
class m181016_044436_fix_item_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $items = Item::find()
            ->notDeleted()
            ->andWhere(['item.price' => NULL])
            ->all();
        foreach($items as $item) {
            $item->updateAttributes(['price' => 0.00]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181016_044436_fix_item_price cannot be reverted.\n";

        return false;
    }
}
