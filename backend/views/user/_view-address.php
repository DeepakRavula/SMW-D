<?php
use yii\helpers\Html;
?>
<div class="address">
    <?= Html::encode($model->label) ?> 
    <?= Html::encode($model->address) ?>
    <?= Html::encode($model->city->name) ?>
    <?= Html::encode($model->province->name) ?>
    <?= Html::encode($model->postal_code) ?>
    <?= Html::encode($model->country->name) ?>
	
</div>