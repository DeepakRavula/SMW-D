<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use Yii;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class RoyaltySearch extends Invoice
{
   	private $dateRange;
    public $fromDate;
    public $toDate;
	public $summarizeResults = false;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateRange', 'fromDate', 'toDate', 'summarizeResults'], 'safe'],
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
        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $toDate);
    }

    public function getDateRange()
    {
        $fromDate = $this->fromDate->format('d-m-Y');
        $toDate = $this->toDate->format('d-m-Y');
        $this->dateRange = $fromDate.' - '.$toDate;

        return $this->dateRange;
    }
}
