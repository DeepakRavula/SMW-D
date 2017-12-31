<?php

use yii\db\Migration;

class m171229_140300_add_locationId extends Migration
{
    public function up()
    {
		$this->addColumn('rbac_auth_item_child', 'location_id', $this->integer());
    }

    public function down()
    {
        echo "m171229_140300_add_locationId cannot be reverted.\n";

        return false;
    }
}
