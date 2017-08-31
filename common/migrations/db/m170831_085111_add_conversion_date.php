<?php

use yii\db\Migration;
use Carbon\Carbon;
use common\models\Location;

class m170831_085111_add_conversion_date extends Migration
{
    public function up()
    {
		$this->addColumn('location', 'conversionDate', 
			$this->timestamp()->after('slug'));
		$locations = Location::find()->all();
		foreach($locations as $location) {
			$location->updateAttributes([
				'conversionDate' => (new Carbon('first day of September 2017'))->addWeeks(2)
			]);
		}
    }

    public function down()
    {
        echo "m170831_085111_add_conversion_date cannot be reverted.\n";

        return false;
    }
}
