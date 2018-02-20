<?php


use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use common\models\LocationAvailability;
use yii\helpers\Url;
use kartik\time\TimePickerAsset;
TimePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\bootstrap\ActiveForm */
$this->title = 'Edit Location';
?>
<?php 
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['location/modify','resourceId' =>$model->day,'type'=>$model->type,'fromTime' => $model->fromTime,'toTime' =>$model->toTime]),
    ]); ?>
   <div class="row p-20">     
        <div class="col-md-6 form-group">
                <?= $form->field($model, 'fromTime')->widget(TimePicker::classname(), [
                    'options' => [
                        'id' => 'location-availability-from-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
                <?= $form->field($model, 'toTime')->widget(TimePicker::classname(), ['options' => [
                        'id' => 'location-availability-to-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
            <?php echo $form->field($model, 'day')->dropdownList(LocationAvailability::getWeekdaysList(), ['prompt' => 'select day']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<script>
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['location/view']); ?>";
+        $.pjax.reload({url: url, container: "#location-view", replace: false, timeout: 4000});
        return false;
    });
    $(document).on('modal-delete', function(event, params) {
        var url = "<?php echo Url::to(['location/view']); ?>";
+        $.pjax.reload({url: url, container: "#location-view", replace: false, timeout: 4000});
        return false;
    });
</script>