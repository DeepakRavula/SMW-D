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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email','role_name','firstname','lastname'], 'safe'],
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
        $query = User::find()->alias('u');

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

 		$query->leftJoin(['rbac_auth_assignment aa'], 'u.id = aa.user_id');
 		$query->leftJoin(['rbac_auth_item ai'], 'aa.item_name = ai.name');
 		$query->leftJoin(['user_location ul'], 'ul.user_id = u.id');
        $query->leftJoin(['user_profile uf'], 'uf.user_id = u.id');

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['ai.name' => $this->role_name])
            ->andFilterWhere(['like', 'uf.lastname' , $this->lastname])
            ->andFilterWhere(['like', 'uf.firstname' , $this->firstname]);

		if($this->role_name !== USER::ROLE_ADMINISTRATOR) {
            $query->andFilterWhere(['like', 'ul.location_id', $locationId]);
		}

		$query->active();
        return $dataProvider;
    }
}
