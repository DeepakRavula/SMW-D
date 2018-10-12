<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\InvoiceLineItem;

/**
 * ItemSearch represents the model behind the search form about `common\models\Item`.
 */
class InvoiceLineItemSearch extends InvoiceLineItem
{
    public $fromDate;
    public $toDate;
    public $groupByMethod;
    public $groupByItem;
    public $groupByItemCategory;
    public $dateRange;
    private $customerId;
    private $isCustomerReport;
    
    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($value)
    {
        $this->customerId = trim($value);
    }
    
    public function getIsCustomerReport()
    {
        return $this->isCustomerReport;
    }

    public function setIsCustomerReport($value)
    {
        $this->isCustomerReport = trim($value);
    }

    public function rules()
    {
        return [
            [['groupByItem', 'groupByItemCategory','groupByMethod', 'fromDate', 'toDate',
                'dateRange', 'customerId', 'isCustomerReport'], 'safe'],
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
        if (!empty($this->dateRange)) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $customerId = $this->customerId;
        if (!$customerId) {
            $customerId = null;
        }
        $query = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId, $customerId) {
                if ($this->isCustomerReport) {
                    $query->andWhere(['invoice.user_id' => $customerId]);
                }
                $query->notDeleted()
                    ->notCanceled()
                    ->notReturned()
                    ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE])
                    ->location($locationId)
                    ->between((new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d'));
                if (!$this->isCustomerReport) {
                    $query->orderBy([
                            'DATE(invoice.date)' => SORT_ASC,
                        ]);
                }
            }]);
            
        if ($this->isCustomerReport) {
            $query->joinWith(['itemCategory' => function ($query) {
                $query->orderBy(['item_category.id' => SORT_ASC]);
            }]);
        }
        if ($this->groupByItem && !$this->isCustomerReport) {
            $query->groupBy(['invoice_line_item.id,item_id, DATE(invoice.date)']);
        }
        if ($this->groupByItemCategory && !$this->isCustomerReport) {
            $query->joinWith('itemCategory');
                $query->orderBy(['invoice.date' => SORT_ASC, 'item_category.id' => SORT_ASC]);  
        }if ($this->groupByMethod) {
            $query->groupBy('DATE(invoice.date), item_category.id');
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
