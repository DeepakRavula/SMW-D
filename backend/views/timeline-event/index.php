<?php

use yii\grid\GridView;
use common\models\timelineevent\TimelineEventLink;
use yii\helpers\Html;

$this->title = Yii::t('backend', 'Timeline');
?>
<div class="box">
<div class="box-body p-10">
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?php $columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				return Yii::$app->formatter->asDateTime($data->created_at);
			},
		],
		[
			'label' => 'Message',
			'format' => 'raw',
			'value' => function ($data) {
				return $data->getMessage();
			},
		],
	];
 ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>

</div>
</div>