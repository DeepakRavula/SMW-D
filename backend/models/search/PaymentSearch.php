<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;
use Yii;
use common\models\PaymentMethod;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentSearch extends Payment
{
    public $fromDate;
    public $toDate;
    public $groupByMethod = false;
    public $query;
    public $dateRange;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'groupByMethod', 'query','dateRange'], 'safe'],
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
        $locationId          = Yii::$app->session->get('location_id');
        $query               = Payment::find()
            ->location($locationId)
			->andWhere(['NOT', ['payment_method_id' => [PaymentMethod::TYPE_ACCOUNT_ENTRY,PaymentMethod::TYPE_CREDIT_USED, PaymentMethod::TYPE_CREDIT_APPLIED]]])
            ->notDeleted();
        $dataProvider        = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
		
		$query->orderBy([
			'DATE(payment.date)' => SORT_ASC,
			'payment_method_id' => SORT_ASC
			]);
        if (!($this->load($params) && $this->validate())) {
            $this->fromDate      = new \DateTime();
            $this->toDate        = new \DateTime();
            $query->andWhere(['between', 'DATE(payment.date)', $this->fromDate->format('Y-m-d'),
                $this->toDate->format('Y-m-d')]);
            return $dataProvider;
        }
		if($this->groupByMethod) {
			$query->groupBy('DATE(payment.date), payment_method_id');
		} 
        if(!empty($this->dateRange)) {
				list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
        }
        $query->andWhere(['between', 'DATE(payment.date)', (new \DateTime($this->fromDate))->format('Y-m-d'),
           (new \DateTime($this->toDate))->format('Y-m-d')]);

        return $dataProvider;
    }
}
