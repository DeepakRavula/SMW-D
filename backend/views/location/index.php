<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index">


    <p>
        <?php echo Html::a('Add', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
		'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [

             [
             'attribute'=>'name',
             'format' => 'raw',
             'value'=>function ($data) {
                        return Html::a($data->name, ['location/view', 'id' => $data->id]);
                      },
             ],
            'address',
			'from_time',
			'to_time',

        ],
    ]); ?>

</div>
