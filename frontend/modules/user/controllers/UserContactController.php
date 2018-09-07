<?php

namespace frontend\modules\user\controllers;

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
                'only' => ['create-email', 'create-phone', 'update-primary', 'create-address','edit-email','edit-phone','edit-address', 'delete'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            
        ];
    }
    public function actionCreateEmail($id)
    {
        $user = User::findOne(['id' => $id]);
        $email = new UserEmail();
        $contact = new UserContact();
        $data = $this->renderAjax('/default/contact/form/_email', [
            'emailModel' => $email,
            'model' => $contact,
            'userModel' => $user,
        ]);
        if ($email->load(Yii::$app->request->post()) &&
            $contact->load(Yii::$app->request->post())) {
            $contact->userId = $id;
            $contact->isPrimary = false;
            if (!is_numeric($contact->labelId)) {
                $label = new Label();
                $label->name = $contact->labelId;
                $label->userAdded = $id;
                $label->save();
                $contact->labelId = $label->id;
            }
            if ($contact->save()) {
                $email->userContactId = $contact->id;
                $email->save();
                return [
                    'status' => true,
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionCreatePhone($id)
    {
        $user = User::findOne(['id' => $id]);
        $contact = new UserContact();
        $phone = new UserPhone();
        $data = $this->renderAjax('/default/contact/form/_phone', [
            'phoneModel' => $phone,
            'model' => $contact,
            'userModel' => $user,
        ]);
        if ($phone->load(Yii::$app->request->post()) &&
            $contact->load(Yii::$app->request->post())) {
            $contact->userId = $id;
            $contact->isPrimary = false;
            if (!is_numeric($contact->labelId)) {
                $label = new Label();
                $label->name = $contact->labelId;
                $label->userAdded = $id;
                $label->save();
                $contact->labelId = $label->id;
            }
            if ($contact->save()) {
                $phone->userContactId = $contact->id;
                $phone->save();
                return [
                    'status' => true,
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionCreateAddress($id)
    {
        $user = User::findOne(['id' => $id]);
        $address = new UserAddress();
        $contact = new UserContact();
        $data = $this->renderAjax('/default/contact/form/_address', [
            'addressModel' => new UserAddress(),
            'model' => new UserContact(),
            'userModel' => $user,
        ]);
        if ($address->load(Yii::$app->request->post()) &&
            $contact->load(Yii::$app->request->post())) {
            $contact->userId = $id;
            $contact->isPrimary = false;
            if (!is_numeric($contact->labelId)) {
                $label = new Label();
                $label->name = $contact->labelId;
                $label->userAdded = $id;
                $label->save();
                $contact->labelId = $label->id;
            }
            if ($contact->save()) {
                $address->userContactId = $contact->id;
                $address->save();
                return [
                    'status' => true,
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionUpdatePrimary($id, $contactId, $contactType)
    {
        $model = User::findOne(['id' => $id]);
        if ((int)$contactType === UserContact::TYPE_EMAIL) {
            $primaryEmail = UserEmail::find()
                    ->notDeleted()
                    ->joinWith(['userContact' => function ($query) {
                        $query->primary();
                    }])
                    ->andWhere(['userId' => $model->id])
                    ->one();
            if (!empty($primaryEmail)) {
                $primaryEmail->userContact->updateAttributes([
                    'isPrimary' => false,
                ]);
            }
            $response = [
                'status' => true
            ];
        } elseif ((int)$contactType === UserContact::TYPE_PHONE) {
            $primaryPhone = UserPhone::find()
                    ->notDeleted()
                    ->joinWith(['userContact' => function ($query) {
                        $query->primary();
                    }])
                    ->andWhere(['userId' => $model->id])
                    ->one();
            if (!empty($primaryPhone)) {
                $primaryPhone->userContact->updateAttributes([
                    'isPrimary' => false,
                ]);
            }
            $response = [
                'status' => true
            ];
        } elseif ((int)$contactType === UserContact::TYPE_ADDRESS) {
            $primaryAddress = UserAddress::find()
                    ->notDeleted()
                    ->joinWith(['userContact' => function ($query) {
                        $query->primary();
                    }])
                    ->andWhere(['userId' => $model->id])
                    ->one();
            if (!empty($primaryAddress)) {
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
    public function actionEditEmail($id)
    {
        $model = $this->findModel($id);
        $emailModel = $model->email;
        $data = $this->renderAjax('/default/contact/form/_email', [
            'emailModel' => $emailModel,
            'model' => $model,
            'userModel' => $model->user,
        ]);
        if ($emailModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            $emailModel->save();

            if (!is_numeric($model->labelId)) {
                $label = new Label();
                $label->name = $model->labelId;
                $label->userAdded = $model->userId;
                $label->save();
                $model->labelId = $label->id;
            }
            $model->save();
            return [
                'status' => true,
            ];
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }
    public function actionEditPhone($id)
    {
        $model = $this->findModel($id);
        $phoneModel = $model->phone;
        $data = $this->renderAjax('/default/contact/form/_phone', [
            'phoneModel' => $phoneModel,
            'model' => $model,
            'userModel' => $model->user,
        ]);
        if ($phoneModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            $phoneModel->save();

            if (!is_numeric($model->labelId)) {
                $label = new Label();
                $label->name = $model->labelId;
                $label->userAdded = $model->userId;
                $label->save();
                $model->labelId = $label->id;
            }
            $model->save();
            return [
                'status' => true,
            ];
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }
    public function actionEditAddress($id)
    {
        $model = $this->findModel($id);
        $addressModel = $model->address;
        $data = $this->renderAjax('/default/contact/form/_address', [
            'addressModel' => $addressModel,
            'model' => $model,
            'userModel' => $model->user,
        ]);
        if ($addressModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            $addressModel->save();

            if (!is_numeric($model->labelId)) {
                $label = new Label();
                $label->name = $model->labelId;
                $label->userAdded = $model->userId;
                $label->save();
                $model->labelId = $label->id;
            }
            $model->save();
            return [
                'status' => true,
            ];
        }
        return [
            'status' => true,
            'data' => $data
        ];
    }
     
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->email)) {
            $contactModel = $model->email;
            $type = UserContact::TYPE_EMAIL;
        } elseif (!empty($model->phone)) {
            $contactModel = $model->phone;
            $type = UserContact::TYPE_PHONE;
        } elseif (!empty($model->address)) {
            $contactModel = $model->address;
            $type = UserContact::TYPE_ADDRESS;
        }
        if ($model->delete()) {
            $contactModel->delete();
            return [
                'status' => true,
                'type' => $type,
            ];
        }
    }

    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = UserContact::find()->location($locationId)
                ->andWhere(['user_contact.id' => $id])
                ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
