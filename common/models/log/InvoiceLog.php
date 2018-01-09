<?php
namespace common\models\log;

use Yii;
use common\models\Invoice;
use common\models\log\Log;
use yii\helpers\Url;
use yii\helpers\Json;

class InvoiceLog extends Log
{

    public function addProformaInvoice($event)
    {
        if(is_a(Yii::$app,'yii\console\Application')) {
			$baseUrl = Yii::$app->getUrlManager()->baseUrl;
		} else {
			$baseUrl = Yii::$app->request->hostInfo; 
		}
		$invoiceModel = $event->sender;	
		$invoice = Invoice::find()->andWhere(['id' => $invoiceModel->id])->asArray()->one();
        $loggedUser     =   end($event->data);
        $object         =   LogObject::findOne(['name' => LogObject::TYPE_INVOICE]);
        $activity       =   LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId     =   $invoiceModel->location_id;
        $this->addLog($object, $activity,$invoice,$locationId,$invoiceModel,$loggedUser);
    }

    public function addLog($object, $activity, $data,$locationId,$model,$loggedUser)
    {
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        $invoiceIndex='invoice #' . $model->getInvoiceNumber();
        $userIndex= $model->user->publicIdentity;
        if(is_a(Yii::$app,'yii\console\Application')) {
        $invoicePath='/admin/invoice/view?id=' . $model->id;
        $userPath='/admin/user/view?UserSearch[role_name]=customer&id='. $model->user->id;
        }else
        {
            $invoicePath=Url::to(['/invoice/view', 'id' => $model->id]);
            $userPath=Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $model->user->id]);
        }
        $log->message = $loggedUser->publicIdentity . ' created an {{'.$invoiceIndex.'}} for {{' .$userIndex. '}}';
        $log->data = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $locationId;
        
        if ($log->save()) {
            $this->addHistory($log, $model, $object);
            $this->addLink($log, $invoiceIndex, $invoicePath);
            $this->addLink($log, $userIndex, $userPath);
            
        }
    }

    public function addLink($log, $index, $path)
    {
        $logLink = new LogLink();
        $logLink->logId = $log->id;
        $logLink->index = $index;
        $logLink->baseUrl = Yii::$app->request->hostInfo;
        $logLink->path = $path;
        $logLink->save();
    }

    public function addHistory($log, $model, $object)
    {
        $logHistory = new LogHistory();
        $logHistory->logId = $log->id;
        $logHistory->instanceId = $model->id;
        $logHistory->instanceType = $object->name;
        $logHistory->save();
    }
}
