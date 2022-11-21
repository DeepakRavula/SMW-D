<?php

use yii\db\Migration;
use common\models\Item;

/**
 * Class m180516_054902_item_discription_fix
 */
class m180516_054902_item_discription_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $items = Item::find()
            ->notDeleted()
            ->andWhere(['description' => ''])
            ->all();
        foreach ($items as $item) {
            $item->updateAttributes([
                'description' => $item->code
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180516_054902_item_discription_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180516_054902_item_discription_fix cannot be reverted.\n";

        return false;
    }
    */
}
