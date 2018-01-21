<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Program;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ProgramSearch extends Program
{
    public $showAllPrograms = false;
    public $query;
    const PROGRAM_TYPE_PRIVATE=1;
    const PROGRAM_TYPE_GROUP=2;
    public $type;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'rate', 'showAllPrograms', 'type', 'query'], 'safe'],
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
        $query = Program::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($this->type)) {
            $query->andWhere(['type' => $this->type]);
        }
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if (!$this->showAllPrograms) {
            $query->active();
        }
         if((int)$this->type === self::PROGRAM_TYPE_PRIVATE) {
            $query->andWhere(['type' => $this->type]);
         } 
	if((int)$this->type === self::PROGRAM_TYPE_GROUP) {
            $query->andWhere(['type' => $this->type]); 
	} 
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'rate', $this->rate]);

        return $dataProvider;
    }
}