<?php

use yii\helpers\Html;

?>

<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default modal-cancel']);?>    
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info modal-save', 'name' => 'signup-button']) ?>
        </div>
        <div id="modal-delete" class="form-group pull-left" style="display: none;">
            <?= Html::a('Delete', '', ['class' => 'btn btn-danger modal-delete']); ?>
        </div>
    </div>
</div>