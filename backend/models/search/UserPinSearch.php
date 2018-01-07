<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\UserPin;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserPinSearch extends UserPin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
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
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $query = UserPin::find()
                ->location($locationId)
                ->staffs();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
