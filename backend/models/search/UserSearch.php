<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Invoice;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
	const STATUS_ALL = 3;
    
    public $role_name;
    public $lastname;
    public $firstname;
    public $query;
    public $showAllCustomers;
	public $showAllTeachers;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email', 'role_name', 'firstname', 'lastname', 'query', 'showAllCustomers', 'showAllTeachers'], 'safe'],
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
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $query = User::find()
            ->notDeleted();

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
        $query->leftJoin(['phone_number pn'], 'pn.user_id = user.id');
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
            ]
        ]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->query])
            ->orFilterWhere(['like', 'uf.lastname', $this->query])
            ->orFilterWhere(['like', 'uf.firstname', $this->query])
            ->orFilterWhere(['like', 'pn.number', $this->query]);

        $query->andFilterWhere(['ai.name' => $this->role_name]);

        if ($this->role_name !== USER::ROLE_ADMINISTRATOR) {
            $query->andFilterWhere(['like', 'ul.location_id', $locationId]);
        }

        if ($this->role_name === USER::ROLE_CUSTOMER) {
            if (!$this->showAllCustomers) {
                $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
                $query->joinWith(['student' => function ($query) use ($currentDate) {
                    $query->enrolled($currentDate);
                }]);
				$query->active();
            }
            $query->groupBy('user.id');
        }
		 if ($this->role_name === USER::ROLE_TEACHER) {
            if (!$this->showAllTeachers) {
                $query->joinWith(['userLocation' => function ($query) {
                    $query->joinWith('teacherAvailability');
                }]);
				$query->active();
            }
            $query->groupBy('user.id');
        }

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
