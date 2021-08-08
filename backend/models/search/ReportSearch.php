<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use Yii;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ReportSearch extends Invoice
{
    private $dateRange;
    public $fromDate;
    public $toDate;
    public $summarizeResults = false;
    public $greatGrandTotal;
    public $greatSubTotal;
    public $greatTaxTotal;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateRange', 'fromDate', 'toDate', 'summarizeResults','greatGrandTotal', 'greatSubTotal', 'greatTaxTotal'], 'safe'],
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
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getDateRange()
    {
        $fromDate = $this->fromDate;
        $toDate = $this->toDate;
        $this->dateRange = $fromDate.' - '.$toDate;
        return $this->dateRange;
    }
}
