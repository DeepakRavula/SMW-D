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
    public $dateRange;

    public function rules()
    {
        return [
            [['groupByItem', 'groupByItemCategory', 'fromDate', 'toDate','dateRange',], 'safe'],
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
    public function setDateRange($dateRange)
    {
        list($fromDate, $toDate) = explode(' - ', $dateRange);
        $this->fromDate = \DateTime::createFromFormat('M d,Y', $fromDate);
        $this->toDate = \DateTime::createFromFormat('M d,Y', $toDate);
    }

    public function getDateRange()
    {
        $fromDate = $this->fromDate->format('M d,Y');
        $toDate = $this->toDate->format('M d,Y');
        $this->dateRange = $fromDate.' - '.$toDate;

        return $this->dateRange;
    }
    /**
     * Creates data provider instance with search query applied.
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {   
        if(!empty($this->dateRange)) {
				list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
        }
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
