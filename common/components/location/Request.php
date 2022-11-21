<?php

namespace common\components\location;

class Request extends \yii\web\Request
{
    private $_locations;
    
    public function getAcceptableLocations()
    {
        if ($this->_locations === null) {
            if (isset($_SERVER['HTTP_ACCEPT_LOCATION'])) {
                $this->_locations = array_keys($this->parseAcceptHeader($_SERVER['HTTP_ACCEPT_LOCATION']));
            } else {
                $this->_locations = [];
            }
        }

        return $this->_locations;
    }
}
