<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="pull-left">
    <div id="show-all" class="checkbox-btn">
        <label>
            <input type="checkbox" id="schedule-show-all" name="Schedule[showAll]" value="1"> 
            Show All
        </label>
    </div>
</div>

<div class="pull-right">
    <?= Html::a('<i class="fa fa-tv"></i>', '', ['class' => 'tv-icon']) ?>
</div>
