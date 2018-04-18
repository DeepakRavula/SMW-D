<?php

use yii\helpers\Html;
use common\models\Lesson;

?>

<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <?= Html::a('Back', '', ['class' => 'btn btn-info modal-back']);?>
            <?= Html::a('Delete', '', ['class' => 'btn btn-danger modal-delete']); ?>
        </div>
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default modal-cancel']);?>    
            <?= Html::submitButton(Yii::t('backend', 'Save'), [
                'class' => 'btn btn-info modal-save',
                'name' => 'signup-button',
                'value' => Lesson::APPLY_SINGLE_LESSON
            ]) ?>
            <?= Html::submitButton(Yii::t('backend', 'Apply All'), [
                'class' => 'btn btn-info modal-save-all',
                'name' => 'button',
                'value' => Lesson::APPLY_ALL_FUTURE_LESSONS
            ]) ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.modal-save-all').hide();
        $('.modal-back').hide();
        $('.modal-delete').hide();
    });
</script>