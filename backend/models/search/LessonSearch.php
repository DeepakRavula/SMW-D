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
class LessonSearch extends Lesson
{
	const INVOICE_STATUS_UNINVOICED = 'uninvoiced';

	public $invoiceStatus = self::INVOICE_STATUS_UNINVOICED;
	public $lessonStatus = Lesson::STATUS_COMPLETED;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceStatus', 'lessonStatus'], 'safe'],
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
        $query = Lesson::find()->alias('l')->location($locationId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		if( ! empty($this->invoiceStatus)) {
			if($this->invoiceStatus === self::INVOICE_STATUS_UNINVOICED) {
				$query->unInvoiced();
			} else {
				$invoiceStatus = $this->invoiceStatus;
				$query->joinWith(['invoiceLineItem' => function($query) use($invoiceStatus){
					$query->joinWith('invoice');
					$query->where(['invoice.status' => $invoiceStatus]);
				}]);
			}
		}

		if($this->lessonStatus == Lesson::STATUS_COMPLETED) {
			$query->completed();
		} else if($this->lessonStatus === 'scheduled') {
			$query->scheduled();
		}

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
