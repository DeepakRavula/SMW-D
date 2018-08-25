<?php
namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;
use Yii;
use common\models\Location;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class StudentBirthdaySearch extends Student
{
    private $dateRange;
    public $fromDate;
    public $toDate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['fromDate', 'toDate', 'dateRange'], 'safe'],
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
    /**
     * Creates data provider instance with search query applied.
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Student::find()
            ->notDeleted()
            ->location($locationId)
            ->statusActive()
            ->orderBy(['DATE_FORMAT(birth_date,"%m-%d")' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andWhere(['between', 'DATE_FORMAT(birth_date,"%m-%d")', (new \DateTime($this->fromDate))->format('m-d'), (new \DateTime($this->toDate))->format('m-d')]);

        return $dataProvider;
    }
}
