<?php
use yii\helpers\Html;
?>
<div class="address p-t-10 p-b-10">
    <div class="col-md-2 p-0"><strong><?= Html::encode( ! empty($model->label) ? $model->label : null) ?></strong></div> 
    <div class="col-md-3">
    	<?= Html::encode( ! empty($model->address) ? $model->address : null) ?>,
    <?= Html::encode( ! empty($model->city->name) ? $model->city->name : null) ?>,<Br>
    <?= Html::encode( ! empty($model->province->name) ? $model->province->name : null) ?>, 
    <?= Html::encode( ! empty($model->country->name) ? $model->country->name : null) ?> <Br>
    <?= Html::encode( ! empty($model->postal_code) ? $model->postal_code : null) ?>
		  <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i>'), ['delete-address', 'id' => $model->id,'userId' => $model->users->id], [
                'class' => '',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
    </div>
    <div class="clearfix"></div>

</div>