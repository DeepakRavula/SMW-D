<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php yii\widgets\Pjax::begin([
    'id' => 'customer-notes-listing',
    'timeout' => 6000,
]) ?>
<?php echo ListView::widget([
	'dataProvider' =>  $noteDataProvider,
	'itemView' => '_list',
]); ?>
<?php \yii\widgets\Pjax::end(); ?>