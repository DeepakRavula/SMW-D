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
class EnrolmentPaymentSearch extends Payment
{
    public $enrolmentId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enrolmentId'], 'safe'],
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
        $enrolmentId = $this->enrolmentId;
        $pfiPayments = Payment::find()
            ->location($locationId)
            ->joinWith(['invoice' => function ($query) use($enrolmentId) {
                $query->joinWith(['lineItem' => function ($query) use($enrolmentId) {
                    $query->joinWith(['lineItemPaymentCycleLesson' => function ($query) use($enrolmentId) {
                        $query->joinWith(['paymentCycleLesson' => function ($query) use($enrolmentId) {
                            $query->joinWith(['paymentCycle' => function ($query) use($enrolmentId) {
                                $query->joinWith(['enrolment' => function ($query) use($enrolmentId) {
                                    $query->andWhere(['enrolment.id' => $enrolmentId]);
                                }]);
                            }]);
                        }]);
                    }]);
                }])
                ->proFormaInvoice();
            }])
            ->notDeleted();

        $query = Payment::find()
            ->location($locationId)
            ->joinWith(['invoice' => function ($query) use($enrolmentId) {
                $query->joinWith(['lineItem' => function ($query) use($enrolmentId) {
                    $query->joinWith(['lineItemLesson' => function ($query) use($enrolmentId) {
                        $query->joinWith(['lesson' => function ($query) use($enrolmentId) {
                            $query->joinWith(['course' => function ($query) use($enrolmentId) {
                                $query->joinWith(['enrolment' => function ($query) use($enrolmentId) {
                                    $query->andWhere(['enrolment.id' => $enrolmentId]);
                                }]);
                            }]);
                        }]);
                    }]);
                }])
                ->invoice();
            }])
            ->notDeleted()
            ->union($pfiPayments);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
