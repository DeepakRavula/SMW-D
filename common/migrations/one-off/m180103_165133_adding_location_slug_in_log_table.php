<?php

use yii\db\Migration;
use common\models\log\Log;
use common\models\log\LogLink;
use yii\helpers\StringHelper;

class m180103_165133_adding_location_slug_in_log_table extends Migration
{
   
    public function up()
    {
        $locations=['arcad','newma','south','bolto','north','west-','maple','richm','woodb','burli'];
        $pathExplode=[];
        $logLinks = LogLink::find()->all();
         foreach ($logLinks as $logLink) {
            $path=""; 
            $path= $logLink->path;
            $start=7;
            $length=5;
            print_r($logLink->path);
            $slug= StringHelper::byteSubstr($path, $start, $length);
           
            $locationSlugPresent=false;
            foreach($locations as $location)
            {
                if($location===$slug)
                    $locationSlugPresent=true;
            }
            if(!$locationSlugPresent)
            {
                
                list($pathExplode) = explode('-', $path); 
                print_r($pathExplode[1]."////");
                $path="";
                die('coming');
            }
            else
                {
                $path="";
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
