<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="item-category-form row">

    <?php   $url = Url::to(['item-category/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
                $url = Url::to(['item-category/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
    <div class="col-md-12">
        <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
</div>

    <?php ActiveForm::end(); ?>

</div>
