<?php

use yii\helpers\Html;
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
        'id' => 'update-item-category-form',
        'action' => $url,
    ]); ?>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="form-group col-xs-12">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default item-category-cancel']);?>
        <?php if (!$model->isNewRecord) {
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
			'id' => 'item-delete-button',
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ]
                ]);
            }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
