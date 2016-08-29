<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GroupCourse;

/**
 * GroupCourseSearch represents the model behind the search form about `common\models\GroupCourse`.
 */
class GroupCourseSearch extends GroupCourse
{ 
    public $query;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['length', 'query'], 'safe'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
        $query = GroupCourse::find()
				->where(['location_id' => $locationId]);
            
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        } 
        
        $query->joinWith(['teacher' => function($query) use($locationId){				
            $query->joinWith('userProfile up');                     
        }]);
        $query->joinWith('program');

        $query->andFilterWhere([
            'id' => $this->id, 
            'length' => $this->length,
        ]);
        
        $query->andFilterWhere(['like', 'program.name', $this->query]);        
        $query->orFilterWhere(['like', 'up.firstname', $this->query]);
        $query->orFilterWhere(['like', 'up.lastname', $this->query]);

        return $dataProvider;
    }
}
