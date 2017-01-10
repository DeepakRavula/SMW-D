<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php echo ListView::widget([
	'dataProvider' =>  $noteDataProvider,
	'itemView' => '_list',
]); ?>