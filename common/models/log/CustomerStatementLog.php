<?php
namespace common\models\log;

use Yii;
use common\models\log\Log;
use common\models\CustomerStatement;
use common\models\User;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "CourseLog".
 *
 */
class CustomerStatementLog extends Log
{
    public function printCustomerStatement($event)
    {
        $customerStatementModel = $event->sender;
        $loggedUser = end($event->data);
        $data = User::find(['id' => $customerStatementModel->userId])->asArray()->one();
        $object = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $activity = LogActivity::findOne(['name' => LogActivity::TYPE_PRINT]);
        $userModel = User::find()->andWhere(['id' => $customerStatementModel->userId])->one();
        $userIndex = $userModel->publicIdentity;
        $message = $loggedUser->publicIdentity . ' printed customer statement for {{'.$userIndex. '}}';
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        $log->message = $message;
        $log->data = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $userModel->userLocation->location_id;
        $userPath= Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $userModel->id]);
        if ($log->save()) {
            $this->addHistory($log, $userModel, $object);
            $this->addLink($log, $userIndex, $userPath);
        }
        return true;
    }

    
    public function addLink($log, $index, $path)
    {
        $logLink          = new LogLink();
        $logLink->logId   = $log->id;
        $logLink->index   = $index;
        $logLink->baseUrl = Yii::$app->request->hostInfo;
        $logLink->path    = $path;
        
        return $logLink->save();
    }
    public function addHistory($log, $model, $object)
    {
        $logHistory= new LogHistory();
        $logHistory->logId = $log->id;
        $logHistory->instanceId = $model->id;
        $logHistory->instanceType = $object->name;
        return $logHistory->save();
    }
}
