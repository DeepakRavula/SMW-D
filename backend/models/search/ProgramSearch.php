<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Program;
use common\models\Invoice;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ProgramSearch extends Program
{
	    public $showAllPrograms = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['name','rate','showAllPrograms','type'],'safe'],
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

		if(! $this->showAllPrograms) {
			$query->active();
		}

		$query->andWhere(['type' => $this->type]);

        return $dataProvider;
    }
}
