<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\User;
use common\models\Location;
use Yii;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class LocationScheduleSearch extends Lesson
{
    public $locationId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId'], 'safe'],
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
        $userId = Yii::$app->user->id;
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $roles = Yii::$app->authManager->getRolesByUser($userId);
        $role = end($roles);
        $user = User::findOne(['id' => $userId]);
        if ($role->name !== User::ROLE_ADMINISTRATOR) {
            $this->locationId = $user->userLocation->location_id;
        } else {
            $this->locationId = $locationId;
        }
        $query = Lesson::find()
            ->andWhere(['DATE(date)' => (new \DateTime())->format('Y-m-d')])
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->notDeleted();
        if (!empty($this->locationId)) {
            $query->location($this->locationId);
        }
        $query->orderBy(['TIME(date)' => SORT_ASC]);
        $dataProvider= new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
