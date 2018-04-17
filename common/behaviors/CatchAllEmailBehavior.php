<?php
namespace common\behaviors;

use Yii;
use frontend\models\Sponsor;
use common\models\Organization;
use yii\base\Behavior;
use yii\mail\BaseMailer;

/**
 * Class CatchAllMailBehavior
 * @package common\behaviors
 */
class CatchAllEmailBehavior extends Behavior
{

    /**
     * @return array
     */
    public function events()
    {
        return [
            BaseMailer::EVENT_BEFORE_SEND => 'addAuditEmailIdsToBcc',
        ];
    }

    public function addAuditEmailIdsToBcc($event)
    {
	    die('dsd');
        $bccEmails[] = Yii::$app->params['adminEmail'];
        if (!empty(Yii::$app->user->identity->sponsor_id)) {
            $sponsorId = Yii::$app->user->identity->sponsor_id;
            $organization = Organization::findOne($sponsorId);
            if(!empty($organization->bcc_audit_email_address)) {
                array_push($bccEmails, $organization->bcc_audit_email_address);
            }else
            {
                $sponsor=Sponsor::findOne($sponsorId);
                if(!empty($sponsor->bcc_audit_email_address)) {
                    array_push($bccEmails, $sponsor->bcc_audit_email_address);
                }
            }
        }
        return $event->message->setBcc($bccEmails);
    }
}