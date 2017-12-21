<?php
namespace common\components\codemix;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use common\models\Location;
/**
 * This class extends Yii's UrlManager and adds features to detect the location
 * from the URL or from browser settings transparently. It also can persist the
 * location in the user session and optionally in a cookie. It also adds the
 * location parameter to any created URL.
 */
class LocationUrlManager extends Component
{
    public $location;
	
    public function init()
    {
        $this->location = 'arcadia-corporate';
        parent::init();
    }
}

