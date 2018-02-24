<?php

use yii\db\Migration;

/**
 * Class m180224_112348_add_isLocationSpecific_field
 */
class m180222_112348_add_isLocationSpecific_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('rbac_auth_item', 'isLocationSpecific', $this->integer()->notNull()->defaultValue(0)->after('type'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180224_112348_add_isLocationSpecific_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180224_112348_add_isLocationSpecific_field cannot be reverted.\n";

        return false;
    }
    */
}
