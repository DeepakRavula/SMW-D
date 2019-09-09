<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Location;
use common\models\Invoice;
use common\models\UserProfile;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    const STATUS_ALL = 3;
    const STATUS_OWING = 4;
        
    private $accountView;
    public $role_name;
    public $lastname;
    public $firstname;
    public $query;
    public $showAll;
    private $email;
    public $phone;
    public $student;
    public $balance;
    public $status;
    
    public function getAccountView()
    {
        return $this->accountView;
    }

    public function setAccountView($value)
    {
        $this->accountView = trim($value);
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = trim($value);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','accountView', 'status'], 'integer'],
            [['email', 'role_name', 'firstname', 'lastname', 'query','phone','showAll','accountView', 'student', 'balance'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = User::find()
            ->excludeWalkin()
            ->notDeleted();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->leftJoin(['rbac_auth_assignment aa'], 'user.id = aa.user_id')
            ->andWhere(['aa.item_name' => $this->role_name]);
        $query->leftJoin(['user_profile uf'], 'uf.user_id = user.id');
        if ($this->phone) {
            $query->joinWith(['userContacts' => function ($query) {
                $query->joinWith('phone');
            }]);
        }

        $dataProvider->setSort([
            'attributes' => [
                'firstname' => [
                    'asc' => ['uf.firstname' => SORT_ASC],
                    'desc' => ['uf.firstname' => SORT_DESC],
                ],
                'lastname' => [
                    'asc' => ['uf.lastname' => SORT_ASC],
                    'desc' => ['uf.lastname' => SORT_DESC],
                ],
		        'email' => [
                    'asc' => ['user_email.email' => SORT_ASC],
                    'desc' => ['user_email.email' => SORT_DESC],
                ],
            ]
        ]);
	    $dataProvider->sort->defaultOrder = [
          'lastname' => SORT_ASC
        ];
        if ($this->email) {
            $query->joinWith(['emails' => function ($query) {
                $query->andFilterWhere(['like', 'email', $this->email]);
            }]);
        }
        if ((int) $this->status === self::STATUS_OWING) {
            $query->joinWith(['customerAccount' => function ($query) {
                $query->andFilterWhere(['>', 'customer_account.balance', 0]);
            }]);
        }
        if ($this->firstname) {
            $query->andFilterWhere(['like', 'uf.firstname', $this->firstname]);
        }
        if ($this->lastname) {
            $query->andFilterWhere(['like', 'uf.lastname', $this->lastname]);
        }
                
        if ($this->role_name !== USER::ROLE_ADMINISTRATOR) {
            $query->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->andWhere([ 'user_location.location_id' => $locationId]);
                if ($this->role_name === USER::ROLE_TEACHER && !$this->showAll) {
                    $query->joinWith(['teacherAvailability' => function ($query) {
                        $query->andWhere(['NOT', [ 'teacher_availability_day.id' =>  null]]);
                    }]);
                }
            }]);
        }

        if (!$this->showAll) {
            $query->owingCustomers();
        }
	    $query->groupBy('user.id');
        return $dataProvider;
    }

    public static function invoiceStatuses()
    {
        return [
            self::STATUS_ALL => 'All',
            Invoice::STATUS_OWING => 'Owing',
            Invoice::STATUS_PAID => 'Paid',
        ];
    }

    public static function balanceStatus()
    {
        return [
            self::STATUS_ALL => 'All',
            self::STATUS_OWING => 'Owing',
        ];
    }
}
