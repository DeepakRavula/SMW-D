<?php

use yii\helpers\Html;
use common\models\Lesson;

?>

<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <?= Html::a('Back', '', ['id' => 'modal-back', 'class' => 'btn btn-info modal-back']);?>
            <?= Html::a('Delete', '', ['class' => 'btn btn-danger modal-delete']); ?>
            <?= Html::a('Print', '', ['class' => 'btn btn-default modal-print']); ?>
        </div>
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default modal-cancel']);?>
            <?= Html::submitButton(Yii::t('backend', 'Apply'), [
                'id' => 'modal-apply',
                'class' => 'btn btn-info modal-button',
                'name' => 'signup-button'
            ]) ?>
            <?= Html::submitButton(Yii::t('backend', 'Save'), [
                'id' => 'modal-save',
                'class' => 'btn btn-info modal-save',
                'name' => 'signup-button',
                'value' => Lesson::APPLY_SINGLE_LESSON
            ]) ?>
            <?= Html::submitButton(Yii::t('backend', 'Apply All'), [
                'id' => 'modal-save-all',
                'class' => 'btn btn-info modal-save-all',
                'name' => 'button',
                'value' => Lesson::APPLY_ALL_FUTURE_LESSONS
            ]) ?>
            <?= Html::a('EMail', '', ['class' => 'btn btn-info modal-mail']);?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.modal-save-all').hide();
        $('.modal-button').hide();
        $('.modal-back').hide();
        $('.modal-delete').hide();
        $('.modal-print').hide();
        $('.modal-mail').hide();
    });
</script>