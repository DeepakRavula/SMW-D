<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProformaInvoice;
use common\models\Location;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ProformaInvoiceSearch extends ProformaInvoice
{
    
    public $showCheckBox;
    public $isPrint;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox','isPrint'], 'safe'],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = ProformaInvoice::find()
                ->location($locationId);
       

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
       
        return $dataProvider;
    }
}
