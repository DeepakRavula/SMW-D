<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InvoiceLineItem;

/**
 * ItemSearch represents the model behind the search form about `common\models\Item`.
 */
class InvoiceLineItemSearch extends InvoiceLineItem
{
    public $fromDate;
    public $toDate;
    public $groupByItem;
    public $groupByItemCategory;

    public function rules()
    {
        return [
            [['groupByItem', 'groupByItemCategory', 'fromDate', 'toDate'], 'safe'],
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
        $query = InvoiceLineItem::find()
            ->joinWith(['invoice' => function($query) use ($locationId) {
                $query->notDeleted()
                    ->location($locationId)
                    ->between((new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d'))
                    ->orderBy([
                        'DATE(invoice.date)' => SORT_DESC,
                    ]);
            }]);
        if ($this->groupByItem) {
            $query->groupBy('item_id, DATE(invoice.date)');
        }
        if ($this->groupByItemCategory) {
            $query->joinWith('itemCategory')
                ->groupBy('item_category.id, DATE(invoice.date)');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        return $dataProvider;
    }
}