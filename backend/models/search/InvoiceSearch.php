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
    const STATUS_MAIL_SENT = 1;
    const STATUS_MAIL_NOT_SENT = 2;
    const STATUS_ALL = 3;
    public $toggleAdditionalColumns;
    public $fromDate;
    public $toDate;
    private $dateRange;
    public $dueToDate;
    public $dueFromDate;
    public $type;
    public $query;
    public $mailStatus;
    public $invoiceStatus;
	public $summariseReport = false;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate'], 'date', 'format' => 'php:d-m-Y'],
            [['mailStatus', 'invoiceStatus'], 'integer'],
            [['type', 'query', 'toggleAdditionalColumns', 'dateRange',
                'dueFromDate', 'dueToDate', 'summariseReport'], 'safe'],
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
        $this->dueFromDate = \DateTime::createFromFormat('d-m-Y', $fromDate);
        $this->dueToDate = \DateTime::createFromFormat('d-m-Y', $toDate);
    }

    public function getDateRange()
    {
        $fromDate = $this->dueFromDate->format('d-m-Y');
        $toDate = $this->dueToDate->format('d-m-Y');
        $this->dateRange = $fromDate.' - '.$toDate;

        return $this->dateRange;
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $query = Invoice::find()
                ->where([
                    'location_id' => $locationId,
                ])
				->notDeleted();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith(['user' => function ($query) {
            $query->joinWith('userProfile up')
                  ->joinWith('phoneNumber pn');
        }]);
        $query->groupBy('invoice.invoice_number');

        $query->andFilterWhere(['like', 'up.firstname', $this->query])
              ->orFilterWhere(['like', 'up.lastname', $this->query])
              ->orFilterWhere(['like', 'pn.number', $this->query]);

        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);

        if ((int) $this->type === Invoice::TYPE_PRO_FORMA_INVOICE) {
            if ((int) $this->mailStatus === self::STATUS_MAIL_SENT) {
                $query->mailSent();
            } elseif ((int) $this->mailStatus === self::STATUS_MAIL_NOT_SENT) {
                $query->mailNotSent();
            }
            if ((int) $this->invoiceStatus === Invoice::STATUS_OWING) {
                $query->unpaid()->proFormaInvoice();
            } elseif ((int) $this->invoiceStatus === Invoice::STATUS_PAID) {
                $query->paid()->proFormaInvoice();
            }
            $query->andWhere(['between', 'invoice.dueDate', $this->dueFromDate->format('Y-m-d'),
                    $this->dueToDate->format('Y-m-d')]);
        }
        $query->andWhere(['between', 'invoice.date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);

        $query->andFilterWhere(['type' => $this->type]);
		
		return $dataProvider;
    }

	public static function invoiceStatuses()
    {
        return [
            self::STATUS_ALL => 'All',
            Invoice::STATUS_OWING => 'Unpaid',
            Invoice::STATUS_PAID => 'Paid',
        ];
    }
	public static function mailStatuses()
    {
        return [
            self::STATUS_ALL => 'All',
            self::STATUS_MAIL_SENT => 'Sent',
			self::STATUS_MAIL_NOT_SENT => 'Unsent'
        ];
    }

    
}
