<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProformaInvoice;
use common\models\Location;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ProformaInvoiceSearch extends ProformaInvoice
{
    
    public $showCheckBox;
    public $isPrint;
    public $number;
    public $customer;
    public $phone;
    public $status;
    public $dateRange;
    public $proformaInvoiceStatus;
    public $fromDate;
    public $toDate;
    public $showAll;

    const STATUS_ALL = 'all';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox','isPrint','number','customer','phone', 'dateRange', 'status', 'proformaInvoiceStatus', 'showAll'], 'safe'],
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
        $query = ProformaInvoice::find()
                ->notDeleted()
                ->location($locationId);

        $query->joinWith(['user' => function ($query) {	
		$query->joinWith('userProfile');
		$query->joinWith('phoneNumber');
        }])->groupBy("proforma_invoice.id");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
	$dataProvider->setSort([
            'attributes' => [
                'number' => [
                    'asc' => ['proforma_invoice.proforma_invoice_number' => SORT_ASC],
                    'desc' => ['proforma_invoice.proforma_invoice_number' => SORT_DESC],
                ],
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
		        'phone' => [
                    'asc' => ['user_phone.number' => SORT_ASC],
                    'desc' => ['user_phone.number' => SORT_DESC],
                ],
                'dateRange' => [
                    'asc' => ['proforma_invoice.dueDate' => SORT_ASC],
                    'desc' => ['proforma_invoice.dueDate' => SORT_DESC],
                ]
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
            'dateRange' => SORT_ASC,
        ];
        
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if($this->number) {
            $query->andFilterWhere(['like', 'proforma_invoice.proforma_invoice_number', $this->number]);
        }
        if (!empty($this->dateRange)) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
            $query->andWhere(['between', 'DATE(proforma_invoice.dueDate)',
                (new \DateTime($this->fromDate))->format('Y-m-d'),
                (new \DateTime($this->toDate))->format('Y-m-d')]);
        }
        if(!empty($this->proformaInvoiceStatus)) {
            $query->andFilterWhere(['proforma_invoice.status' => $this->proformaInvoiceStatus]);
        }
        if (!$this->showAll) {
            $query->unpaid();
        }
        $query->joinWith(['user' => function($query) {
            $query->joinWith(['userProfile' => function($query) {
                $query->andFilterWhere(['or', ['like', 'user_profile.firstname', trim($this->customer)],['like', 'user_profile.lastname', trim($this->customer)]]);
            }]);
        }]);
        $query->andFilterWhere(['like', 'user_phone.number', $this->phone]);
            return $dataProvider;
    }
    public static function proformaInvoiceStatuses()
    {
        return [
	        ProformaInvoice::STATUS_PAID => 'Paid',
            ProformaInvoice::STATUS_UNPAID => 'Unpaid',
        ];
    }
}