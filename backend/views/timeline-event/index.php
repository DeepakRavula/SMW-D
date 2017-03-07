<?php

use yii\grid\GridView;

$this->title = Yii::t('backend', 'Application timeline');
?>
<?php \yii\widgets\Pjax::begin() ?>
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
                    return !empty($data->getOldAttribute('data')) ? $data->getOldAttribute('data') : null;
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
<?php \yii\widgets\Pjax::end() ?>

