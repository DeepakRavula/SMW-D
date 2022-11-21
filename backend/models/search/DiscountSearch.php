<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use common\models\InvoiceLineItem;

/**
 * ItemSearch represents the model behind the search form about `common\models\Item`.
 */
class DiscountSearch extends Invoice
{
    private $dateRange;
    public $fromDate;
    public $toDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateRange', 'fromDate', 'toDate'], 'safe'],
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
        $fromDate = $this->fromDate->format('Y-m-d');
        $toDate = $this->toDate->format('Y-m-d');
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId) {
                $query->andWhere([
                                    'location_id' => $locationId,
                                    'invoice.type' => Invoice::TYPE_INVOICE,
                ])
                ->notDeleted()
                                ->notCanceled();
            }])
                        ->joinWith(['itemDiscounts' => function ($query) {
                            $query->andWhere(['NOT', ['invoice_line_item_discount.id' => null]])
                                ->andWhere(['NOT', ['invoice_line_item_discount.value' => 0.00]]);
                        }]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        $query->orderBy([
            'invoice.user_id' => SORT_ASC,
        ]);
        $query->andWhere(['between', 'invoice.date', $fromDate, $toDate]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
