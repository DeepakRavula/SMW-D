<?php
namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;
use Yii;

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
        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $toDate);
    }

    public function getDateRange()
    {
        $fromDate = $this->fromDate->format('d-m-Y');
        $toDate = $this->toDate->format('d-m-Y');
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
        $locationId = Yii::$app->session->get('location_id');
        $query = Student::find()->notDeleted()
            ->location($locationId)
			->orderBy(['DATE_FORMAT(birth_date,"%m-%d")' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		
		$query->andWhere(['between', 'DATE_FORMAT(birth_date,"%m-%d")', $this->fromDate->format('m-d'), $this->toDate->format('m-d')]);

        return $dataProvider;
    }
}
