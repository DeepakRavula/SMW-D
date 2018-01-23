<?php

use yii\helpers\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */

$this->title = 'Holiday Details';
$this->params['breadcrumbs'][] = ['label' => 'Holidays', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="holiday-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-xs-2">
        	<i class="fa fa-calendar"></i> <?php echo Yii::$app->formatter->asDate($model->date); ?>
    </div>
		<div class="col-md-12 m-t-20">
			<?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
			<?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => '',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
			<div class="clearfix"></div>
		</div>
    <div class="clearfix"></div>

</div>
</div>
