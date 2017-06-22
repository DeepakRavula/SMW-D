<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;

/**
 * ItemSearch represents the model behind the search form about `common\models\Item`.
 */
class ItemSearch extends Item
{
    public $showAllItems;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showAllItems'], 'safe'],
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
        $locationId = Yii::$app->session->get('location_id');
        $query = Item::find()
                ->notDeleted()
                ->defaultItems($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (!$this->showAllItems) {
            $query->active();
        }

        return $dataProvider;
    }
}
