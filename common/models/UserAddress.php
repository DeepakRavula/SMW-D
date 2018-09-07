<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "user_address".
 *
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 */
class UserAddress extends \yii\db\ActiveRecord
{
    private $labelId;

    public function getLabelId()
    {
        return $this->labelId;
    }

    public function setLabelId($value)
    {
        $this->labelId = trim($value);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'cityId', 'provinceId', 'countryId'], 'required'],
            [['userContactId', 'provinceId', 'countryId'], 'integer'],
            [['address'], 'string', 'max' => 64],
            [['postalCode'], 'string', 'max' => 16],
            [['labelId', 'cityId', 'isDeleted'], 'safe'],
            [['address','postalCode'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'address_id' => 'Address ID',
            'cityId' => 'City',
            'provinceId' => 'Province',
            'countryId' => 'Country'
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            'audittrail'=>[
                'class'=>AuditTrailBehavior::className(), 
                'consoleUserId'=>1, 
                'attributeOutput'=>[
                    'last_checked'=>'datetime',
                ],
            ],
        ];
    }

    public static function find()
    {
        return new \common\models\query\UserAddressQuery(get_called_class());
    }

    public function getUserContact()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'cityId']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'provinceId']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'countryId']);
    }
    
    public function beforeDelete() 
    {
        if ($this->userContact) {
            $this->userContact->delete();
        }
        return parent::beforeDelete();
    }

    public function setModel($model)
    {
        $this->address = $model->address;
        $this->labelId = $model->addressLabelId;
        $this->cityId = $model->cityId;
        $this->provinceId = $model->provinceId;
        $this->countryId = $model->countryId;
        $this->postalCode = $model->postalCode;
        return $this;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
