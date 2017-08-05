<?php

namespace backend\models\search;

use yii\base\Model;
use common\models\Invoice;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class DashboardSearch extends Invoice
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
}
