<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Lesson;
use common\models\Invoice;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class InvoiceSearch extends Invoice
{
	public $fromDate = '1-1-2016';
	public $toDate = '31-12-2016';
    public $type;
    public $query;
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'type', 'query'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
        $query = Invoice::find()->alias('i')
				->where([
					'location_id' => $locationId,
				]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ( !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith(['user' => function($query) {				
            $query->joinWith('userProfile up')
                  ->joinWith('phoneNumber pn');                     
        }]);
        $query->groupBy('i.invoice_number');
       
        $query->andFilterWhere(['like', 'up.firstname', $this->query])
              ->orFilterWhere(['like', 'up.lastname', $this->query])
              ->orFilterWhere(['like', 'pn.number', $this->query]);
         
		$this->fromDate =  \DateTime::createFromFormat('d-m-Y', $this->fromDate);
		$this->toDate =  \DateTime::createFromFormat('d-m-Y', $this->toDate);
        
		$query->andWhere(['between','i.date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
        
        $query->andFilterWhere(['type' => $this->type]);
        
        return $dataProvider;
    }

	public static function invoiceStatuses() {
		return [
			'' => 'All',
			self::INVOICE_STATUS_UNINVOICED => 'Not Invoiced',	
			Invoice::STATUS_PAID => 'Paid',
			Invoice::STATUS_OWING => 'Owing',

		];
	}

	public static function lessonStatuses() {
		return [
			'' => 'All',
			Lesson::STATUS_COMPLETED => 'Completed',
			'scheduled' => 'Scheduled',
		];
	}
}
