<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use kartik\date\DatePicker;
?>
<div class="col-md-12">
    <div class="col-lg-2 pull-right">
        <?php echo '<label>Go to Date</label>'; ?>
        <?php echo DatePicker::widget([
                'name' => 'switched-date',
                'id' => 'week-view-calendar-go-to-date',
                'value' => Yii::$app->formatter->asDate((new DateTime())->format('d-m-Y')),
                'type' => DatePicker::TYPE_INPUT,
                'buttonOptions' => [
                    'removeButton' => true,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true
                ]
        ]); ?>
    </div>
    <div id="week-view-calendar"></div>
</div>