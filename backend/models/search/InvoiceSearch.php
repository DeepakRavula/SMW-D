<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use common\models\Location;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class InvoiceSearch extends Invoice
{
    const STATUS_MAIL_SENT = 1;
    const STATUS_MAIL_NOT_SENT = 2;
    const STATUS_ALL = 6;
    public $toggleAdditionalColumns;
    public $isPrint;
    public $isMail;
    public $fromDate;
    public $toDate;
    public $invoiceDateRange;
    public $dateRange;
    public $dueToDate;
    public $dueFromDate;
    public $type;
    public $isWeb;
    public $query;
    public $mailStatus;
    public $invoiceStatus;
    public $phone;
    public $summariseReport = false;
    public $number;
    public $customer;
    public $student;
    public $proFormaInvoiceStatus;
    public $customerId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate'], 'date', 'format' => 'php:M d,Y'],
            [['mailStatus', 'invoiceStatus', 'proFormaInvoiceStatus'], 'integer'],
            [['type', 'query', 'toggleAdditionalColumns', 'dateRange', 'invoiceDateRange',
                'customer', 'student',  'dueFromDate', 'dueToDate', 'number', 'phone', 'summariseReport', 
                'isPrint', 'isWeb', 'isMail', 'customerId'], 'safe'],
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
        $query = Invoice::find()
                ->andWhere([
                    'invoice.location_id' => $locationId,
                ])
                ->notDeleted()
                ->notCanceled();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->fromDate = \DateTime::createFromFormat('M d,Y', $this->fromDate);
        $this->toDate = \DateTime::createFromFormat('M d,Y', $this->toDate);
        if ((int) $this->type === Invoice::TYPE_PRO_FORMA_INVOICE) {
            if ((int) $this->mailStatus === self::STATUS_MAIL_SENT) {
                $query->mailSent();
            } elseif ((int) $this->mailStatus === self::STATUS_MAIL_NOT_SENT) {
                $query->mailNotSent();
            }
            if ((int) $this->proFormaInvoiceStatus === Invoice::STATUS_OWING) {
                $query->unpaid()->proFormaInvoice();
            } elseif ((int) $this->proFormaInvoiceStatus === Invoice::STATUS_PAID) {
                $query->paid()->proFormaInvoice();
            }
            if (!empty($this->dateRange)) {
                list($this->dueFromDate, $this->dueToDate) = explode(' - ', $this->dateRange);
                $query->andWhere(['between', 'DATE(invoice.dueDate)',
                    (new \DateTime($this->dueFromDate))->format('Y-m-d'),
                    (new \DateTime($this->dueToDate))->format('Y-m-d')]);
            }
        } else {
	    if ((int) $this->invoiceStatus === Invoice::STATUS_OWING) {
                $query->unpaid()->invoice()->andWhere(['isVoid'=>false]);
            } elseif ((int) $this->invoiceStatus === Invoice::STATUS_PAID) {
                $query->paid()->invoice()->andWhere(['isVoid'=>false]);
            }
	    elseif ((int) $this->invoiceStatus === Invoice::STATUS_VOID) {
                $query->andWhere(['isVoid'=>true]);
            }
	    elseif ((int) $this->invoiceStatus === Invoice::STATUS_CREDIT) {
                $query->credit()->invoice()->andWhere(['isVoid'=>false]);
            }
            if (!empty($this->invoiceDateRange)) {
                list($this->fromDate, $this->toDate) = explode(' - ', $this->invoiceDateRange);
                $query->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($this->fromDate))->format('Y-m-d'),
                    (new \DateTime($this->toDate))->format('Y-m-d')]);
            } 
        }
        $query->andFilterWhere(['type' => $this->type]);
		$query->joinWith(['user' => function ($query) {
			$query->joinWith(['userProfile' => function ($query) {
		}]);
		$query->joinWith(['userContacts' => function ($query){
				$query->joinWith(['phone']);
            }]);
            $query->joinWith(['student' => function ($query) use($locationId) {
                $query->location($locationId);
            }]);   
        }]);
        if ($this->number) {
		    $query->andFilterWhere(['invoice.id' => $this->number]);
        }
           $query->andFilterWhere(['like', 'user_phone.number', trim($this->phone)]);
        if ($this->customer) {
            $query->andFilterWhere(['or', ['like', 'user_profile.firstname', trim($this->customer)], ['like', 'user_profile.lastname', trim($this->customer)]]);
        } elseif ($this->customerId) {
            $query->andFilterWhere(['user.id' => $this->customerId ]); 
        }
        if ($this->student) {
		    $query->andFilterWhere(['like', 'student.first_name', $this->student])
                  ->orFilterWhere(['like', 'student.last_name', $this->student]);
        }
        $query->groupBy('invoice.id');
       	$dataProvider->setSort([
            'attributes' => [
                'number' => [
                    'asc' => ['invoice_number' => SORT_ASC],
                    'desc' => ['invoice_number' => SORT_DESC],
                ],
                'dateRange' => [
                    'asc' => ['dueDate' => SORT_ASC],
                    'desc' => ['dueDate' => SORT_DESC],
                ],
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
		'invoiceDateRange' => [
                    'asc' => ['date' => SORT_ASC],
                    'desc' => ['date' => SORT_DESC],
                ],
                'student' => [
                    'asc' => ['student.first_name' => SORT_ASC],
                    'desc' => ['student.first_name' => SORT_DESC],
                ],    
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
            'invoiceDateRange' => SORT_DESC,
        ];
        return $dataProvider;
    }

    public static function proFormInvoiceStatuses()
    {
        return [
            self::STATUS_ALL => 'All',
            Invoice::STATUS_OWING => 'Unpaid',
            Invoice::STATUS_PAID => 'Paid',
        ];
    }
     public static function invoiceStatuses()
    {
        return [
            self::STATUS_ALL => 'All',
	    Invoice::STATUS_CREDIT => 'Credit',
            Invoice::STATUS_OWING => 'Owing',
            Invoice::STATUS_PAID => 'Paid',
	    Invoice::STATUS_VOID => 'Voided',
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
