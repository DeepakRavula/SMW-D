<?php
namespace common\components\codemix;

use yii\helpers\ArrayHelper;
use common\models\Location;
/**
 * This class extends Yii's UrlManager and adds features to detect the language
 * from the URL or from browser settings transparently. It also can persist the
 * language in the user session and optionally in a cookie. It also adds the
 * language parameter to any created URL.
 */
class UrlManager extends \codemix\localeurls\UrlManager
{
    public function init()
    {
        $this->languages = ArrayHelper::map(Location::find()->all(), 'slug', 'slug');
        
        parent::init();
    }
}
