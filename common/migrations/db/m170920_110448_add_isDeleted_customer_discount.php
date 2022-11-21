<?php

use yii\db\Migration;
use common\models\discount\CustomerDiscount;

class m170920_110448_add_isDeleted_customer_discount extends Migration
{
    public function up()
    {
        $this->addColumn(
            'customer_discount',
            'isDeleted',
            $this->boolean()->after('value')
        );
        $customerDiscounts = CustomerDiscount::find()->all();
        foreach ($customerDiscounts as $discount) {
            $discount->updateAttributes([
                'isDeleted' => false
            ]);
        }
    }

    public function down()
    {
        echo "m170920_110448_add_isDeleted_customer_discount cannot be reverted.\n";

        return false;
    }
}
