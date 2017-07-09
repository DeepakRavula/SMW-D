<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use Yii;
use common\models\PaymentMethod;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class TeacherScheduleSearch extends Lesson
{

    public $findTeacher;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['findTeacher'], 'safe'],
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
        

        $lessons->joinWith(['teacher' => function ($query) use ($locationId) {
            $query->joinWith('userProfile up');
        }]);
        
        $lessons->andFilterWhere(['like', 'up.firstname', $this->findTeacher]);
        $lessons->orFilterWhere(['like', 'up.lastname', $this->findTeacher]);

        return $dataProvider;
    }
}
