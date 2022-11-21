<?php

use yii\db\Migration;
use common\models\log\LogLink;
use common\models\Location;
use yii\helpers\ArrayHelper;

class m180103_165133_adding_location_slug_in_log_table extends Migration
{
    public function up()
    {
        $locations = ArrayHelper::map(Location::find()
                    ->all(), 'id', 'slug');
        $logLinks  = LogLink::find()->all();
        foreach ($logLinks as $logLink) {
            $path       = $logLink->path;
            $pathArray  = explode("/", $path);
            $addLocationSlug = true;
            foreach ($locations as $location) {
                if (array_search($location, $pathArray)) {
                    $addLocationSlug = false;
                }
            }
            if ($addLocationSlug) {
                $firstWord  = array_shift($pathArray);
                $secondWord = array_shift($pathArray);
                $thirdWord  = $locations[$logLink->log->locationId];
                array_unshift($pathArray, $firstWord, $secondWord, $thirdWord);
                $path       = implode("/", $pathArray);
                $logLink->path=$path;
                $logLink->save();
            } else {
            }
        }
    }

    public function down()
    {
        echo "m180103_165133_adding_location_slug_in_log_table cannot be reverted.\n";

        return false;
    }
    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
