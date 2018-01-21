<?php

use yii\helpers\Html;

?>

<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default ' . $cancelClass]);?>    
           <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info ' . $saveClass, 'name' => 'signup-button']) ?>
        </div>
    <?php if ($deletable) : ?>
    <div class="form-group pull-left">
        <?= Html::a('Delete', '', ['class' => 'btn btn-danger ' . $deleteClass]); ?>
    </div>
    <?php endif; ?>
    </div>
</div>

