<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Location;
use common\models\LocationAvailability;
use kartik\daterange\DateRangePicker;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="col-md-12">
	<?php
    $form = ActiveForm::begin([
            'id' => 'teacher-lesson-search-form',
    ]);
    ?>
    <div class="row">
        <div class="col-md-3 form-group">
            <?php
            echo DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]);

            ?>
        </div>
        <div class="col-md-1 form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'lesson-search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::a('<i class="fa fa-print"></i> Print', ['print/teacher-lessons', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default m-r-10', 'target' => '_blank']) ?>
        </div>
         <div class="pull-right checkbox">
           <?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php echo $this->render('_time-voucher-content',['searchModel'=>$searchModel,'teacherLessonDataProvider' => $teacherLessonDataProvider]); ?>

<script>
    $(document).on('click', '#teacher-lesson-grid  tbody > tr', function () {
        var lessonId = $(this).data('key');
        var params = $.param({ id: lessonId });
        lesson.update(params);
        return false;
    });
    
    $("#lessonsearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]':summariesOnly | 0 });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#teacher-lesson-grid",replace:false,  timeout: 4000});  //Reload GridView
        var printUrl = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
        $('#print-btn').attr('href', printUrl);
    });

    $("#teacher-lesson-search-form").on("submit", function () {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]': summariesOnly | 0 });
        $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
        var url = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
        $('#print-btn').attr('href', url);
        return false;
    });

    $(document).on('click', '#print-btn', function () {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#lessonsearch-daterange').val();
        var params = $.param({ 'LessonSearch[dateRange]': dateRange,'LessonSearch[summariseReport]': summariesOnly | 0});
        var url = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
        window.open(url, '_blank');
        return false;
    });
</script>
