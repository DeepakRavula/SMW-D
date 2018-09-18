<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php 
echo Html::a('<i class="fa fa-pencil"></i>', '#', ['class' => 'f-s-18 edit-location m-r-45']);
$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    'fieldConfig' => [
        'options' => [
            'tag' => false,
        ],
    ],
    ]);
?>
<?php yii\widgets\Pjax::begin() ?>
<div class="m-t-n-35 m-l-22">
<div class="checkbox">
<div id="show-all" class="checkbox-btn">
<?= $form->field($model, 'isEnabledCron')->checkbox(['data-pjax' => true]); ?>
</div>
</div>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php ActiveForm::end(); ?>