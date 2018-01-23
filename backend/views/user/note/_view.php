<?php

use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php echo ListView::widget([
    'dataProvider' =>  $noteDataProvider,
    'itemView' => '_list',
]); ?>
<?php $form = ActiveForm::begin([
    'id' => 'user-note-form',
]); ?>
<div class="box-footer">
	<div class="input-group">
        <?php echo $form->field($model, 'content')->textInput(['placeholder' => "Type message"])->label(false)?>
		<div class="input-group-btn ">
			<?php echo Html::submitButton('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success lesson-note-btn note-btn', 'name' => 'signup-button']) ?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>