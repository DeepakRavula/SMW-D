<?php

use yii\grid\GridView;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
?>
  <?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<?php
$columns = [
        [
        'label' => 'Date',
        'contentOptions' => ['class' => 'text-left', 'style' => 'width:150px;'],
        'value' => function ($data) {
            return Yii::$app->formatter->asDateTime($data->created_at);
        },
    ],
        [
        'label' => 'Message',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-left'],
        'headerOptions' => ['class' => 'text-left'],
        'value' => function ($data) {
            $message = $data->message;
            return preg_replace('/[{{|}}]/', '', $message);
        },
    ],
];
?>   
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered table-more-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => $columns,
]);
?>

<script>
	$(document).ready(function () {
		window.print();
	});
</script>