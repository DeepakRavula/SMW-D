<?php

use yii\db\Schema;
use common\rbac\Migration;

class m161008_071610_schema extends Migration
{
    public function up()
    {
		$sql = file_get_contents(dirname(__FILE__).'/' . get_class($this) . '_' . __FUNCTION__ . '.sql');

		return $this->execute($sql);
    }

    public function down()
    {
        echo "m161008_071610_schema cannot be reverted.\n";

        return false;
    }
}
