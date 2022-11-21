<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;
use common\models\Location;
use Yii;
use common\models\PaymentMethod;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentReportSearch extends Payment
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
        $locationId          = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query               = Payment::find()
            ->location($locationId)
            ->exceptAutoPayments()
            ->exceptGiftCard()
            ->notDeleted();
		
        $dataProvider        = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        
        $query->orderBy(['DATE(payment.date)' => SORT_ASC, 'payment_method_id' => SORT_ASC]);
        if (!($this->load($params) && $this->validate())) {
            $fromDate      = new \DateTime();
            $toDate        = new \DateTime();
            $query->andWhere(['between', 'DATE(payment.date)', $fromDate->format('Y-m-d'),
                $toDate->format('Y-m-d')]);
            return $dataProvider;
        }
        if ($this->groupByMethod) {
            $query->groupBy('DATE(payment.date), payment_method_id');
        }
        if ($this->dateRange) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
        }
        $query->andWhere(['between', 'DATE(payment.date)', (new \DateTime($this->fromDate))->format('Y-m-d'),
           (new \DateTime($this->toDate))->format('Y-m-d')]);

        return $dataProvider;
    }
}
