<?php
namespace common\components\backend;

use Yii;
use yii\web\Controller;
use common\models\User;
use common\models\Location;
use common\models\UserLocation;
use yii\web\ForbiddenHttpException;

class BackendController extends Controller
{
    public function init()
    {
        parent::init();
    }
}