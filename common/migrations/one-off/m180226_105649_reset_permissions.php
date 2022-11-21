<?php

use yii\db\Migration;
use common\models\Location;

/**
 * Class m180226_105649_reset_permissions
 */
class m180226_105649_reset_permissions extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->truncateTable('rbac_auth_item_child');
	$locations = Location::find()->all();
	foreach ($locations as $location) {
	    $location->addPermission();
	}
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180226_105649_reset_permissions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180226_105649_reset_permissions cannot be reverted.\n";

        return false;
    }
    */
}
