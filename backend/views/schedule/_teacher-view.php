<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;
?>
<div class="col-md-12">
    <div class="filter">
        <div class="col-md-1 m-t-10 text-right"><p>Filter by</p></div>
        <div class="col-md-3 p-0">
            <?=
            SelectivityWidget::widget([
                'name' => 'Program',
                'id' => 'program-selector',
                'pluginOptions' => [
                    'allowClear' => true,
                    'items' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Program',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?=
            SelectivityWidget::widget([
                'name' => 'Teacher',
                'id' => 'teacher-selector',
                'pluginOptions' => [
                    'allowClear' => true,
                    'items' => ArrayHelper::map($availableTeachersDetails, 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Teacher',
                ],
            ]);
            ?>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div id='calendar'></div>
</div>
