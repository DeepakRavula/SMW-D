<div class="col-lg-2 pull-right">
    <label>Go to Date</label>
    <?= yii\jui\DatePicker::widget([
        'name'  => 'goToDate',
        'value'  => Yii::$app->formatter->asDate(new \DateTime()),
        'dateFormat' => 'php:M d, Y',
        'options' => [
            'id' => 'fullcalendar-week-view-go-to-datepicker',
            'class' => 'form-control',
            'readonly' => true
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'yearRange' => '1500:3000',
            'changeYear' => true
        ]
    ]); ?>
</div>
<div id="week-view-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div id="week-view-calendar"></div>