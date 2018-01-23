<?php
namespace common\widgets;

use Yii;
use common\models\Location;
use yii\bootstrap\Dropdown;
use yii\helpers\ArrayHelper;

class LocationDropdown extends Dropdown
{
    private static $_labels;

    private $_isError;

    public function init()
    {
        $route = Yii::$app->controller->route;
        $appLocation = Yii::$app->location;
        $params = $_GET;
        $this->_isError = $route === Yii::$app->errorHandler->errorAction;

        array_unshift($params, '/' . $route);

        foreach (Yii::$app->urlManager->locations as $location) {
            $isWildcard = substr($location, -2) === '-*';
            if (
                $location === $appLocation ||
                // Also check for wildcard location
                $isWildcard && substr($appLocation, 0, 2) === substr($location, 0, 2)
            ) {
                continue;   // Exclude the current location
            }
            if ($isWildcard) {
                $location = substr($location, 0, 2);
            }
            $params['location'] = $location;
            $this->items[] = [
                'label' => self::label($location),
                'url' => $params,
            ];
        }
        parent::init();
    }

    public function run()
    {
        // Only show this widget if we're not on the error page
        if ($this->_isError) {
            return '';
        } else {
            return parent::run();
        }
    }

    public static function label($code)
    {
        if (self::$_labels === null) {
            $locations = ArrayHelper::map(Location::find()->all(), 'slug', 'name');
            self::$_labels = $locations;
        }

        return isset(self::$_labels[$code]) ? self::$_labels[$code] : null;
    }
}
