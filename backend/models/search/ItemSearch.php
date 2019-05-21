<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use common\models\Location;
use yii\data\ActiveDataProvider;
use common\models\Item;

/**
 * ItemSearch represents the model behind the search form about `common\models\Item`.
 */
class ItemSearch extends Item
{
    public $showAllItems;
    public $avoidDefaultItems;
    public $itemCategory;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['avoidDefaultItems', 'code', 'description', 'price', 'showAllItems', 'itemCategory'], 'safe'],
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
        $query      = Item::find()
            ->notDeleted();
        if ($this->avoidDefaultItems && !($this->showAllItems)) {
            $query->location($locationId)
                ->active();
        } else {
            $query->defaultItems($locationId);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (!$this->showAllItems) {
            $query->active();
        }
        if ($this->itemCategory) {
            $query->joinWith('itemCategory')
            ->andFilterWhere(['like', 'item_category.name', $this->itemCategory]);
        }
        if ($this->code) {
            $query->andFilterWhere(['like', 'code', $this->code]);
        }
        if ($this->description) {
            $query->andFilterWhere(['like', 'description', $this->description]);
        }
        return $dataProvider;
    }
}
