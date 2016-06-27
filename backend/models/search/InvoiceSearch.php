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
	public $fromDate = '2016-1-1';
	public $toDate = '2016-12-31';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate'], 'safe'],
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
        $query = Invoice::find()->alias('l');//->location($locationId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ( !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		$this->fromDate =  \DateTime::createFromFormat('d-m-Y', $this->fromDate);
		$this->toDate =  \DateTime::createFromFormat('d-m-Y', $this->toDate);
		
		$query->andWhere(['between','date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);

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
