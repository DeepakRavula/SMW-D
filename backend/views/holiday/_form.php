<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="holiday-form">
<?php   $url = Url::to(['holiday/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
                $url = Url::to(['holiday/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'holiday-form',
        'action' => $url,
    ]); ?>

    <div class="row">
        <div class="col-md-8">
            <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'firstDay' => 1,
                    ],
                ]); ?>
        </div>
		 <div class="col-md-8">
			 <?php  if ($model->isNewRecord) : ?>
				<?php $model->description = 'Holiday';?>
			<?php endif;?>				
            <?php echo $form->field($model, 'description')->textInput(); ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
    <div class="form-group pull-right">
        <?php
        echo Html::a('Cancel', '', ['class' => 'btn btn-default holiday-cancel']);
        ?>
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
     <div class="form-group pull-left">
         <?php  if (!$model->isNewRecord) {
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'id' => 'holiday-delete-button',
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ]
            ]);
        }?>
     </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
