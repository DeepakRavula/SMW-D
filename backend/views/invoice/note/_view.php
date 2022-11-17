<?php

use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Comments',
    'withBorder' => true,
])
?>
<?php echo ListView::widget([
    'dataProvider' =>  $noteDataProvider,
    'itemView' => '_list',
        'summary' => false,
        'emptyText' => false,
]); ?>
<?php $form = ActiveForm::begin([
    'id' => 'invoice-note-form',
]); ?>
<div class="box-footer">
	<div class="input-group">
        <?php echo $form->field($model, 'content')->textInput(['placeholder' => "Type message"])->label(false)?>
		<div class="input-group-btn ">
			<?php echo Html::submitButton('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success invoice-note-btn note-btn', 'name' => 'signup-button']) ?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<?php LteBox::end() ?>
