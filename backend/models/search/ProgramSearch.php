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
    public $programType;

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

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if($this->programType===Program::TYPE_PRIVATE_PROGRAM)
        {
            $query->andWhere(['type'=> Program::TYPE_PRIVATE_PROGRAM]);
        }
        else if($this->programType===Program::TYPE_GROUP_PROGRAM)
        {
            $query->andWhere(['type'=> Program::TYPE_GROUP_PROGRAM]);
        }
        if (!$this->showAllPrograms) {
            $query->active();
        }

        $query->andWhere(['type' => $this->type]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'rate', $this->rate]);

        return $dataProvider;
    }
}
