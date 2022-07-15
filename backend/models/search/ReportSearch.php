<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use Yii;
use common\models\Location;
use common\models\Lesson;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ReportSearch extends Invoice
{
    private $dateRange;
    public $goToDate;
    public $fromDate;
    public $toDate;
    public $summarizeResults = false;
    public $showAllActive;
    public $showAllInActive;
    public $greatGrandTotal;
    public $greatSubTotal;
    public $greatTaxTotal;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateRange', 'fromDate', 'toDate', 'summarizeResults', 'showAllActive', 'showAllInActive', 'greatGrandTotal', 'greatSubTotal', 'greatTaxTotal', 'goToDate'], 'safe'],
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

    public function search($params)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $currentDate = (new \DateTime())->format('Y-m-d');
        $lessonQuery = Lesson::find()
        ->joinWith(['lessonPayments' => function ($query) {
            $query->andWhere(['NOT', ['lesson_payment.lessonId' => null]]);
        }])
        ->location($locationId)
        ->andWhere(['>', 'lesson.date', $currentDate])
        ->orderBy(['lesson.id' => SORT_ASC])
        ->privateLessons()
        ->notCanceled()
        ->notDeleted()
        ->isConfirmed();
            
        $dataProvider = new ActiveDataProvider([
            'query' => $lessonQuery,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $lessonQuery->andFilterWhere(['>', 'lesson.date', $this->goToDate]);

        return $dataProvider;
    }

    public function setDateRange($dateRange)
    {
        list($fromDate, $toDate) = explode(' - ', $dateRange);
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getDateRange()
    {
        $fromDate = $this->fromDate;
        $toDate = $this->toDate;
        $this->dateRange = $fromDate.' - '.$toDate;
        return $this->dateRange;
    }
}
