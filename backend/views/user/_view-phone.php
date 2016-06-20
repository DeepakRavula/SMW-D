<?php
use yii\helpers\Html;
?>
<div class="phone">
    <?= Html::encode($model->label->name)  ?>  
    <?= Html::encode($model->number) ?>
    <?= Html::encode($model->extension) ?>
	
</div>