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

    public $fromDate;
    public $toDate;
    public $groupByMethod = false;
    public $query;
    public $month;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['fromDate', 'toDate', 'query', 'month'], 'safe'],
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
        $locationId = Yii::$app->session->get('location_id');
        $query = Student::find()
            ->location($locationId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $query->orderBy([
            'DATE(student.birth_date)' => SORT_DESC,
        ]);
        if (!($this->load($params) && $this->validate())) {
            $this->fromDate = new \DateTime();
            $this->toDate = new \DateTime();
            $query->andWhere(['=', 'MONTH(student.birth_date)', $this->month,
            ]);
            return $dataProvider;
        }

        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);
        $query->andWhere(['=', 'MONTH(student.birth_date)', $this->month,
        ]);

        return $dataProvider;
    }
}
