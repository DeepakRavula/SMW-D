<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
	public $role_name;
    public $lastname;
    public $firstname;
    public $query;
    public $showAllCustomers;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email', 'role_name', 'firstname', 'lastname', 'query', 'showAllCustomers'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
        $query = User::find();

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
            'logged_at' => $this->logged_at
        ]);

 		$query->leftJoin(['rbac_auth_assignment aa'], 'user.id = aa.user_id');
 		$query->leftJoin(['rbac_auth_item ai'], 'aa.item_name = ai.name');
 		$query->leftJoin(['user_location ul'], 'ul.user_id = user.id');
        $query->leftJoin(['user_profile uf'], 'uf.user_id = user.id');
        $query->leftJoin(['phone_number pn'], 'pn.user_id = user.id');
		
        $query->andFilterWhere(['like', 'username', $this->username])
            ->orFilterWhere(['like', 'email', $this->query])
            ->orFilterWhere(['like', 'uf.lastname' , $this->query])
            ->orFilterWhere(['like', 'uf.firstname' , $this->query])
            ->orFilterWhere(['like', 'pn.number' , $this->query]);
		
        $query->andFilterWhere(['ai.name' => $this->role_name]);

		if($this->role_name !== USER::ROLE_ADMINISTRATOR) {
            $query->andFilterWhere(['like', 'ul.location_id', $locationId]);
		}
        
        if($this->role_name === USER::ROLE_CUSTOMER) {          
            if( ! $this->showAllCustomers) {             
               $currentDate = (new \DateTime())->format('Y-m-d H:i:s');            
		       $query->joinWith(['student' => function($query) use($currentDate){			
                    $query->enrolled($currentDate);       
  			    }]);  
                $query->andFilterWhere(['like', 'ul.location_id', $locationId]);             
            }        
			$query->groupBy('user.id');
  		} 

		$query->active();
        return $dataProvider;
    }
}
