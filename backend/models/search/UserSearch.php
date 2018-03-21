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
        
    private $accountView;
    public $role_name;
    public $lastname;
    public $firstname;
    public $query;
    public $showAllCustomers;
    public $showAllTeachers;
    public $showAllAdministrators;
    public $showAllStaffMembers;
    public $showAllOwners;
    private $email;
    public $phone;
    
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
            [['id', 'status', 'created_at', 'updated_at', 'logged_at', 'accountView'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email', 'role_name', 'firstname',
                'lastname', 'query','phone','showAllCustomers', 'showAllTeachers','showAllAdministrators','showAllOwners','showAllStaffMembers','accountView'], 'safe'],
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
            ->notDeleted()
            ->notDraft();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logged_at' => $this->logged_at,
        ]);

        $query->leftJoin(['rbac_auth_assignment aa'], 'user.id = aa.user_id');
        $query->leftJoin(['rbac_auth_item ai'], 'aa.item_name = ai.name');
        $query->leftJoin(['user_location ul'], 'ul.user_id = user.id');
	$query->leftJoin(['user_profile uf'], 'uf.user_id = user.id');
	$query->joinWith(['userContacts uc' => function ($query) {
		$query->joinWith('phone');
        }]);
	$query->joinWith('emails');
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
		'phone' => [
                    'asc' => ['user_phone.number' => SORT_ASC],
                    'desc' => ['user_phone.number' => SORT_DESC],
                ]
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
          'lastname' => SORT_DESC,
	    ];
        $query->joinWith(['emails' => function ($query) {
            $query->andFilterWhere(['like', 'email', $this->email]);
        }]);
        $query->andFilterWhere(['like', 'uf.lastname', $this->lastname])
            ->andFilterWhere(['like', 'uf.firstname', $this->firstname]);

        $query->andFilterWhere(['ai.name' => $this->role_name]);

        if ($this->role_name !== USER::ROLE_ADMINISTRATOR) {
            $query->andFilterWhere([ 'ul.location_id' =>  $locationId]);
        }

        if ($this->role_name === USER::ROLE_CUSTOMER) {
            if (!$this->showAllCustomers) {
                $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
                $query->joinWith(['student' => function ($query) use ($currentDate) {
                    $query->enrolled($currentDate);
                }]);
                
            }
           
        }
        if ($this->role_name === USER::ROLE_TEACHER) {
            if (!$this->showAllTeachers) {
                $query->joinWith(['userLocation' => function ($query) {
                    $query->joinWith('teacherAvailability');
                }]);
                
            }
            
        }
        if(!($this->showAllCustomers || $this->showAllTeachers||$this->showAllAdministrators || $this->showAllOwners || $this->showAllStaffMembers))
        {
            $query->active();
          
            
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
}
