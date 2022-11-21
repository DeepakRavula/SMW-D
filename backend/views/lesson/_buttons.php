<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
use common\Models\User;
?>
<?php yii\widgets\Pjax::begin([
    'id' => 'lesson-explode',
    'timeout' => 6000,
]) ?>
<?php if ($model->isPrivate()) : ?>
    <?php if ($model->canExplode()) : ?>
        <?php echo Html::a(
    '<i title="Explode" class="fa fa-code-fork fa-lg"></i>',
            ['private-lesson/split', 'id' => $model->id],
    ['id' => 'split-lesson',
                'class' => 'm-r-20 del-ce btn btn-box-tool',
        ]
) ?>
    <?php endif; ?>
    <?php $loggedUser = User::findOne(Yii::$app->user->id); ?>
    <?php if ($model->canMerge() && $loggedUser->canMerge) : ?>
        <?php echo Html::a('<i title="Merge" class="fa fa-chain"></i>', '#', [
            'id' => 'merge-lesson',
            'class' => 'm-r-20 btn btn-box-tool',
        ])?>
    <?php endif; ?>
<?php endif; ?>
<?= Html::a('<i title="Mail" class="fa fa-envelope"></i>', '#', [
    'id' => 'lesson-mail-button',
    'class' => ' btn btn-box-tool m-r-10'])
?>	
<?php if ($model->isDeletable()) : ?>
	<?= Html::a('<i title="Delete" class="fa fa-trash-o"></i>', ['private-lesson/delete', 'id' => $model->id], [
        'class' => 'btn btn-box-tool m-r-10',
        'id' => 'lesson-delete',
    ])
    ?>	
<?php endif; ?>
<?php \yii\widgets\Pjax::end(); ?>
