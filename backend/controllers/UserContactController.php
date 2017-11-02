<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\EmailForm;
use Yii;
use common\models\UserEmail;
use common\models\UserContact;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\UserPhone;
use common\models\User;
use common\models\Label;
use common\models\UserAddress;
/**
 * BlogController implements the CRUD actions for Blog model.
 */
class UserContactController extends Controller
{
    public function behaviors()
    {
        return [
			'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['create-email', 'create-phone', 'update-primary', 'create-address'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
            
        ];
    }
	public function actionCreateEmail($id)
	{
		$email = new UserEmail();
		$contact = new UserContact();
		$email->load(Yii::$app->request->post());
		$contact->load(Yii::$app->request->post());
		$contact->userId = $id;
		$contact->isPrimary = false;
		if (!is_numeric($contact->labelId)) {
			$label = new Label();
			$label->name = $contact->labelId;
			$label->userAdded = $id;
			$label->save();
			$contact->labelId = $label->id;
		}
		if($contact->save()) {
			$email->userContactId = $contact->id;
			$email->save();
			return [
				'status' => true,
			];
		}
	}
	public function actionCreatePhone($id)
	{
		$phone = new UserPhone();
		$contact = new UserContact();
		$phone->load(Yii::$app->request->post());
		$contact->load(Yii::$app->request->post());
		$contact->userId = $id;
		$contact->isPrimary = false;
		if (!is_numeric($contact->labelId)) {
			$label = new Label();
			$label->name = $contact->labelId;
			$label->userAdded = $id;
			$label->save();
			$contact->labelId = $label->id;
		}
		if($contact->save()) {
			$phone->userContactId = $contact->id;
			$phone->save();
			return [
				'status' => true,
			];
		}
	}
	public function actionCreateAddress($id)
	{
		$address = new UserAddress();
		$contact = new UserContact();
		$address->load(Yii::$app->request->post());
		$contact->load(Yii::$app->request->post());
		$contact->userId = $id;
		$contact->isPrimary = false;
		if (!is_numeric($contact->labelId)) {
			$label = new Label();
			$label->name = $contact->labelId;
			$label->userAdded = $id;
			$label->save();
			$contact->labelId = $label->id;
		}
		if($contact->save()) {
			$address->userContactId = $contact->id;
			$address->save();
			return [
				'status' => true,
			];
		}
	}
	public function actionUpdatePrimary($id, $contactId, $contactType)
	{
		$model = User::findOne(['id' => $id]);
		if((int)$contactType === UserContact::TYPE_EMAIL) {
                    $primaryEmail = UserEmail::find()
                     ->joinWith(['userContact' => function($query) {
                       $query->primary();
}])
                    ->andWhere(['userId' => $model->id])
                    ->one();
            if(!empty($primaryEmail)) {
				$primaryEmail->userContact->updateAttributes([
					'isPrimary' => false,
				]);
			}
			$response = [
				'status' => true
			];
		} elseif ((int)$contactType === UserContact::TYPE_PHONE) {
			$primaryPhone = UserPhone::find()
                     ->joinWith(['userContact' => function($query) {
                       $query->primary();
}])
                    ->andWhere(['userId' => $model->id])
                    ->one();
			if(!empty($primaryPhone)) {
				$primaryPhone->userContact->updateAttributes([
					'isPrimary' => false,
				]);
			}
			$response = [
				'status' => true
			];
		} elseif ((int)$contactType === UserContact::TYPE_ADDRESS) {
			$primaryAddress = UserAddress::find()
                     ->joinWith(['userContact' => function($query) {
                       $query->primary();
}])
                    ->andWhere(['userId' => $model->id])
                    ->one();
			if(!empty($primaryAddress)) {
				$primaryAddress->userContact->updateAttributes([
					'isPrimary' => false,
				]);
			}
			$response = [
				'status' => true
			];
		}
		$contact = UserContact::findOne(['id' => $contactId]);
		$contact->updateAttributes(['isPrimary' => true]);
		return $response;
	}
        public function actionEditEmail($id) {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $userContact = UserContact::find()->where(['id' => $id])->one();
        $emailModel = UserEmail::find()->where(['userContactId' => $id])->one();
        $data = $this->renderAjax('//user/update/_user-email', [
            'emailModel' => $emailModel,
            'userContact' => $userContact,
        ]);
        if ($emailModel->load(Yii::$app->request->post()) && $userContact->load(Yii::$app->request->post())) {
            $emailModel->save();

            if (!is_numeric($userContact->labelId)) {
                $label = new Label();
                $label->name = $userContact->labelId;
                $label->userAdded = $userContact->userId;
                $label->save();
                $userContact->labelId = $label->id;
            }
            $userContact->save();
            return [
                'status' => true,
            ];
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }

}
