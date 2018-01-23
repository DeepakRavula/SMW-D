<?php

use yii\db\Migration;
use Carbon\Carbon;
use common\models\Location;

class m170831_085111_add_conversion_date extends Migration
{
    public function up()
    {
        $this->addColumn(
            'location',
            'conversionDate',
            $this->timestamp()->after('slug')
        );
    }

    public function down()
    {
        echo "m170831_085111_add_conversion_date cannot be reverted.\n";

        return false;
    }
}
