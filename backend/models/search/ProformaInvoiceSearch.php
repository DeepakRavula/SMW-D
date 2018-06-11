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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox'], 'safe'],
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
 
}
