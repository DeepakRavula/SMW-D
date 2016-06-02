<?php
namespace backend\models;

use backend\models\UserForm;
use common\models\User;
use common\models\UserProfile;
use common\models\PhoneLabel;
use common\models\UserAddress;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\Program;
use common\models\Qualification;
use common\models\UserLocation;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class StaffUserForm extends UserForm
{
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=> User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],

			['password', 'required'],
            ['password', 'string', 'min' => 6],
					
            [['status'], 'integer'],
            ['roles','required'],
            
            ['lastname', 'filter', 'filter' => 'trim'],
            ['lastname', 'required', 'on' => 'create'],
            ['lastname', 'string', 'min' => 2, 'max' => 255],
            
            ['firstname', 'filter', 'filter' => 'trim'],
            ['firstname', 'required', 'on' => 'create'],
            ['firstname', 'string', 'min' => 2, 'max' => 255],
        ];
    }
}
