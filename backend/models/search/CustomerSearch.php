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
class CustomerSearch extends User
{
    
        
    
    public $role_name;
    public $lastname;
    public $firstname;
    public $query;
    public $showAll;
    public $email;
    public $phone;
    
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email', 'role_name', 'firstname',
                'lastname', 'query','phone','showAll'], 'safe'],
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
            ->customers($locationId)
            ->notDeleted();

            

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->leftJoin(['user_profile uf'], 'uf.user_id = user.id');
        $query->joinWith(['userContacts' => function ($query) {
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
          'lastname' => SORT_ASC
        ];
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
       
        $query->joinWith(['emails' => function ($query) {
            $query->andFilterWhere(['like', 'email', $this->email]);
        }]);
        $query->andFilterWhere(['like', 'uf.firstname', $this->firstname])
            ->andFilterWhere(['like', 'uf.lastname', $this->lastname]);

        if (!$this->showAll) {
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
