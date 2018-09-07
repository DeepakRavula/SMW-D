<?php

namespace backend\controllers;

use common\models\City;
use Yii;
use common\models\UserEmail;
use common\models\UserContact;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\UserPhone;
use common\models\User;
use common\models\Label;
use common\models\Location;
use common\models\UserAddress;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class UserContactController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => [
                    'create-email', 'create-phone', 'update-primary', 'create-address', 
                    'edit-email', 'edit-phone', 'edit-address', 'delete', 'validate'
                ],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
           'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'create-email', 'create-phone', 'update-primary', 'create-address', 
                            'edit-email', 'edit-phone', 'edit-address', 'delete', 'validate'
                        ],
                        'roles' => ['manageTeachers', 'manageCustomers', 'manageAdmin', 'manageStaff', 'manageOwners'],
                    ],
                ],
            ],  
        ];
    }

    public function actionCreateEmail($id)
    {
        $user = User::findOne(['id' => $id]);
        $email = new UserEmail(['scenario' => UserEmail::SCENARIO_USER_CREATE]);
        
        $contact = new UserContact();
        $data = $this->renderAjax('/user/contact/form/_email', [
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
                if ($email->save()) {
                    return [
                    'status' => true,
                ];
                } else {
                    return [
                                'status' => false,
                                'errors' => $email->getErrors(),
                            ];
                }
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
        $data  = $this->renderAjax('/user/contact/form/_phone', [
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
        $data  = $this->renderAjax('/user/contact/form/_address', [
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
            if (!is_numeric($address->cityId)) {
                $city = new City();
                $city->name = $address->cityId;
                $city->province_id = $address->provinceId;
                $city->save();
                $address->cityId = $city->id;
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
        $emailModel->setScenario(UserEmail::SCENARIO_USER_CREATE);
        $data = $this->renderAjax('/user/contact/form/_email', [
            'emailModel' => $emailModel,
            'model' => $model,
            'userModel' => $model->user,
        ]);
        if ($emailModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            if (!is_numeric($model->labelId)) {
                $label = new Label();
                $label->name = $model->labelId;
                $label->userAdded = $model->userId;
                $label->save();
                $model->labelId = $label->id;
            }
            $model->save();
            $emailModel->save();
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
        $data = $this->renderAjax('/user/contact/form/_phone', [
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
        $data = $this->renderAjax('/user/contact/form/_address', [
            'addressModel' => $addressModel,
            'model' => $model,
            'userModel' => $model->user,
        ]);
        if ($addressModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            if (!is_numeric($model->labelId)) {
                $label = new Label();
                $label->name = $model->labelId;
                $label->userAdded = $model->userId;
                $label->save();
                $model->labelId = $label->id;
            }
            if (!is_numeric($addressModel->cityId)) {
                $city = new City();
                $city->name = $addressModel->cityId;
                $city->province_id = $addressModel->provinceId;
                $city->save();
                $addressModel->cityId = $city->id;
            }
            $addressModel->save();
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
        $contactModel->delete();
        $response = [
                'status' => true,
                'type' => $type,
                'url' => Url::to(['user/view', $model->id]),
            ];
        return $response;
    }

    protected function findModel($id)
    {
        $model = UserContact::findOne($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidate($id = null)
    {
        if (!empty($id)) {
            $model = UserEmail::findOne(['userContactId' => $id]);
        } else {
            $model = new UserEmail();
        }
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            return  ActiveForm::validate($model);
        }
    }
}
