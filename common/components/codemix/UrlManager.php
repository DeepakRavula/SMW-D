<?php
namespace common\components\codemix;


/**
 * This class extends Yii's UrlManager and adds features to detect the language
 * from the URL or from browser settings transparently. It also can persist the
 * language in the user session and optionally in a cookie. It also adds the
 * language parameter to any created URL.
 */
class UrlManager extends \codemix\localeurls\UrlManager
{
    public $languages2;
    
    public function init()
    {
        
    $this->languages = ($this->languages2)();
        
        parent::init();
    }
}
