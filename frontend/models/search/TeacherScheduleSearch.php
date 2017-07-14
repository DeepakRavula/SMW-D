<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\Location;
use Yii;
use common\models\PaymentMethod;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class TeacherScheduleSearch extends Lesson
{

    public $findTeacher,$slug;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['findTeacher'], 'safe'],
        ];
    }

    public function __construct($slug, $config = [])
    {
        $this->slug=$slug;
        parent::__construct($config);   
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
        $location = Location::find()->where(['like', 'slug', $this->slug])->one();
        $locationId = $location->id;
        $lessons = Lesson::find()->location($locationId)->andWhere(['lesson.status' => Lesson::STATUS_SCHEDULED])->notDeleted();
        $dataProvider= new ActiveDataProvider([
            'query' => $lessons,
            'pagination' => false,
        ]);
        $currentDate = (new \DateTime())->format('Y-m-d');
        $lessons->andWhere(['=', 'DATE(date)', $currentDate]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $lessons->andFilterWhere(['lesson.teacherId'=>$this->findTeacher]);

        return $dataProvider;
    }
}
