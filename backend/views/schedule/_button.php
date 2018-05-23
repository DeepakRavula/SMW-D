<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div id="show-all" class="pull-left">
    <label>
        <input type="checkbox" id="schedule-show-all" name="Schedule[showAll]"> 
        Show All
    </label>
</div>

<div class="pull-right">
    <?= Html::a('<i class="fa fa-tv m-l-10"></i>', '', ['class' => 'tv-icon']) ?>
</div>
